<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatTopic;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotMessageStoreController extends Controller
{
	/**
	 * Store a new user message (and a placeholder assistant reply).
	 */
	public function __invoke(Request $request, ChatTopic $topic)
	{
		// Temporary debug logging to capture AJAX/fetch failures
		try {
			Log::debug('ChatbotMessageStoreController::__invoke request', [
				'user_id' => $request->user()?->id,
				'topic_id' => $topic->id,
				'headers' => [
					'X-Inertia' => $request->header('X-Inertia'),
					'X-Requested-With' => $request->header('X-Requested-With'),
					'Accept' => $request->header('Accept'),
				],
				'wantsJson' => $request->wantsJson(),
				'isAjax' => $request->ajax(),
				'body' => $request->all(),
			]);
		} catch (\Throwable $e) {
			// swallow logging errors
		}
		if ($topic->user_id !== $request->user()->id) {
			abort(403);
		}

		$request->validate([
			'content' => ['required', 'string'],
		]);

		$userMessage = ChatMessage::create([
			'chat_topic_id' => $topic->id,
			'user_id' => $request->user()->id,
			'role' => 'user',
			'content' => $request->input('content'),
		]);

		// Update topic's last_message_at immediately so UI shows activity
		$topic->update(['last_message_at' => now()]);

		$assistantMessage = null;

		// Call the configured model to generate an assistant reply and persist it.
		try {
			$assistantContent = $this->generateAssistantResponse($topic, $request->input('content'));
		} catch (\Throwable $e) {
			Log::error('AI generation failed: ' . $e->getMessage(), ['topic' => $topic->id]);
			$assistantContent = "Sorry, I couldn't reach the AI service right now. Please try again later.";
		}

		try {
			$assistantMessage = ChatMessage::create([
				'chat_topic_id' => $topic->id,
				'role' => 'assistant',
				'content' => $assistantContent,
			]);

			$topic->update(['last_message_at' => now()]);
		} catch (\Throwable $e) {
			Log::error('Failed to save assistant message: ' . $e->getMessage(), ['topic' => $topic->id]);
			// don't throw — user message already persisted
		}

		// Log saved message ids so we can verify persistence during debugging
		try {
			Log::debug('ChatbotMessageStoreController::saved', [
				'user_message_id' => $userMessage?->id,
				'assistant_message_id' => $assistantMessage?->id,
				'topic_id' => $topic->id,
			]);
		} catch (\Throwable $e) {
			// ignore logging errors
		}

		$responsePayload = [
			'user_message' => $userMessage,
			'assistant_message' => $assistantMessage,
			'topic_id' => $topic->id,
		];

		// Record the outgoing JSON payload and status so we can correlate with client behaviour
		try {
			Log::debug('ChatbotMessageStoreController::response', [
				'payload' => $responsePayload,
				'status' => 201,
				'topic' => $topic->id,
			]);
		} catch (\Throwable $e) {
			// ignore logging errors
		}

		return response()->json($responsePayload, 201);


		// Fallback: normal redirect for browser form submissions
		return redirect()->route('chatbot.index', ['topic' => $topic->id]);
	}

	/**
	 * Generate assistant response by calling Gemini (Google Generative Language API).
	 * This method always calls the Generative API and expects the model configured
	 * in `config('services.gemini.model')` to be accessible.
	 */
	private function generateAssistantResponse(ChatTopic $topic, string $newUserContent): string
	{
		$apiKey = config('services.gemini.key');
		$model = config('services.gemini.model');

		// Prepare history retrieval helper
		$getHistory = function (int $limit) use ($topic, $newUserContent) {
			$history = ChatMessage::where('chat_topic_id', $topic->id)
				->orderBy('created_at', 'asc')
				->take($limit)
				->get(['role', 'content'])
				->map(fn ($m) => strtoupper($m->role) . ": " . $m->content)
				->toArray();
			$history[] = "USER: " . $newUserContent;
			return implode("\n\n", $history);
		};

		$url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

		// Attempts: first normal, second larger max tokens, third trimmed history and low temperature
		$attempts = [
			[ 'temperature' => 0.2, 'maxOutputTokens' => 512, 'historyLimit' => 20 ],
			[ 'temperature' => 0.2, 'maxOutputTokens' => 1024, 'historyLimit' => 20 ],
			[ 'temperature' => 0.0, 'maxOutputTokens' => 512, 'historyLimit' => 6 ],
		];

		foreach ($attempts as $i => $cfg) {
			$attemptNum = $i + 1;
			$prompt = $getHistory((int) $cfg['historyLimit']);

			try {
				$resp = Http::withHeaders([
					'Content-Type' => 'application/json',
				])->post($url, [
					'contents' => [
						[
							'parts' => [
								['text' => $prompt]
							]
						]
					],
					'generationConfig' => [
						'temperature' => $cfg['temperature'],
						'maxOutputTokens' => $cfg['maxOutputTokens'],
					],
				]);
			} catch (\Throwable $e) {
				Log::warning('Gemini HTTP request failed on attempt ' . $attemptNum, ['message' => $e->getMessage(), 'topic' => $topic->id]);
				$resp = null;
			}

			if (!$resp || !$resp->successful()) {
				$bodyText = $resp ? $resp->body() : '[no response]';
				Log::warning('Gemini API non-success', ['attempt' => $attemptNum, 'status' => $resp?->status(), 'body' => $bodyText, 'topic' => $topic->id]);
				// try next attempt
				continue;
			}

			$body = $resp->json();
			$extracted = $this->extractTextFromGeminiBody($body);

			if ($extracted !== null && trim($extracted) !== '') {
				Log::debug('Gemini API success', ['attempt' => $attemptNum, 'topic' => $topic->id]);
				return $extracted;
			}

			// Log response for analysis and continue retrying
			Log::debug('Gemini response (unable to extract text)', ['attempt' => $attemptNum, 'body' => $body, 'topic' => $topic->id]);
			// continue loop to retry with adjusted config
		}

		// After attempts exhausted, return friendly fallback
		Log::error('Gemini attempts exhausted without usable text', ['topic' => $topic->id]);
		return "(AI tidak mengembalikan teks yang bisa dibaca — coba lagi.)";
	}

	/**
	 * Attempt to extract human-readable text from various Gemini response shapes.
	 * Returns first non-empty string found or null when nothing usable is present.
	 */
	private function extractTextFromGeminiBody(array $body): ?string
	{
		// 1) candidates[].content.parts[].text
		if (!empty($body['candidates']) && is_array($body['candidates'])) {
			foreach ($body['candidates'] as $cand) {
				if (!empty($cand['content'])) {
					// content may be string or array with parts
					if (is_string($cand['content']) && trim($cand['content']) !== '') {
						return $cand['content'];
					}
					if (isset($cand['content']['parts']) && is_array($cand['content']['parts'])) {
						foreach ($cand['content']['parts'] as $part) {
							if (is_string($part['text'] ?? '') && trim($part['text']) !== '') {
								return $part['text'];
							}
						}
					}
				}
			}
		}

		// 2) output[].content[].text (some responses use output key)
		if (!empty($body['output']) && is_array($body['output'])) {
			foreach ($body['output'] as $out) {
				if (!empty($out['content']) && is_array($out['content'])) {
					foreach ($out['content'] as $content) {
						if (is_string($content['text'] ?? '') && trim($content['text']) !== '') {
							return $content['text'];
						}
						// some content items may have nested parts
						if (isset($content['parts']) && is_array($content['parts'])) {
							foreach ($content['parts'] as $p) {
								if (is_string($p['text'] ?? '') && trim($p['text']) !== '') {
									return $p['text'];
								}
							}
						}
					}
				}
			}
		}

		// 3) shallow scan: find first 'text' key anywhere in arrays
		$stack = [$body];
		while (!empty($stack)) {
			$node = array_pop($stack);
			if (is_array($node)) {
				foreach ($node as $k => $v) {
					if ($k === 'text' && is_string($v) && trim($v) !== '') {
						return $v;
					}
					if (is_array($v)) $stack[] = $v;
				}
			}
		}

		return null;
	}

}
