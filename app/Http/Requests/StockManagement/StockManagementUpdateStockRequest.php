<?php

namespace App\Http\Requests\StockManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockManagementUpdateStockRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return in_array($this->user()->role, ['super-admin', 'admin']);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'product' => ['required', 'integer', 'exists:products,id'],
			'type' => ['required', 'string', Rule::in(['increase', 'decrease'])],
			'update_stock' => ['required', 'integer', 'min:1'],
			'notes' => ['nullable', 'string', 'max:1000'],
		];
	}
}
