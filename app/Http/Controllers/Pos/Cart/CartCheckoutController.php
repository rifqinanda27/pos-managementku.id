<?php

namespace App\Http\Controllers\Pos\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartCheckoutController extends Controller
{
	/**
	 * Checkout all items in the user's cart.
	 *
	 * Creates transaction with all cart items, validates stock availability,
	 * and updates product quantities. Uses transaction to ensure atomicity.
	 */
	public function __invoke(Request $request, $userId)
	{
		$user = $request->user();

		// Authorization check
		if ($user->id != (int)$userId) {
			abort(403, 'Unauthorized to checkout this cart.');
		}

		// Fetch all cart items with product data
		$items = CartItem::with('product')->where('user_id', $userId)->get();

		if ($items->isEmpty()) {
			return redirect()->back()->with('alert', [
				'type' => 'warning',
				'message' => 'Cannot checkout: cart is empty.',
			]);
		}

		DB::beginTransaction();
		try {
			// Calculate total using bcmath for precision
			$total = '0.00';
			foreach ($items as $item) {
				$lineTotal = bcmul((string)$item->price, (string)$item->quantity, 2);
				$total = bcadd($total, $lineTotal, 2);
			}

			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'total' => $total,
			]);

			// Process each cart item
			foreach ($items as $item) {
				$product = Product::lockForUpdate()->find($item->product_id);

				// Validate product exists and has sufficient stock
				if (!$product) {
					DB::rollBack();
					return redirect()->back()->with('alert', [
						'type' => 'error',
						'message' => 'Product no longer exists.',
					]);
				}

				if ($product->current_stock < $item->quantity) {
					DB::rollBack();
					return redirect()->back()->with('alert', [
						'type' => 'error',
						'message' => "Insufficient stock for {$product->name}. Available: {$product->current_stock}, Requested: {$item->quantity}",
					]);
				}

				// Create transaction detail record
				TransactionDetail::create([
					'transaction_id' => $transaction->id,
					'product_id' => $item->product_id,
					'quantity' => $item->quantity,
					'price' => $item->price,
					'total' => bcmul((string)$item->price, (string)$item->quantity, 2),
				]);

				// Update product stock and sales count
				$product->current_stock = $product->current_stock - $item->quantity;
				$product->total_sold = $product->total_sold + $item->quantity;
				$product->save();
			}

			// Clear cart after successful checkout
			CartItem::where('user_id', $userId)->delete();

			DB::commit();

			return redirect('/pos-terminal')->with('alert', [
				'type' => 'success',
				'message' => "Checkout successful! Transaction total: " . number_format((float)$total, 2),
			]);
		} catch (\Throwable $e) {
			DB::rollBack();
			Log::error('POS cart checkout failed', [
				'user_id' => $user->id,
				'exception' => get_class($e),
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => app()->environment('production') ? null : $e->getTraceAsString(),
			]);

			return redirect()->back()->with('alert', [
				'type' => 'error',
				'message' => 'Checkout failed. Please try again.',
			]);
		}
	}
}
