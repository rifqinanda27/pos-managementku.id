<?php

namespace App\Http\Controllers\Pos\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartItem;

class CartDeleteController extends Controller
{
	/**
	 * Remove a specific item from the cart.
	 *
	 * Validates authorization before deletion.
	 */
	public function __invoke($userId, CartItem $cartItem)
	{
		// Authorization check
		if ($cartItem->user_id != $userId) {
			abort(403, 'Unauthorized to delete this cart item.');
		}

		$productName = $cartItem->product->name ?? 'Product';
		$cartItem->delete();

		return redirect()->back()->with('alert', [
			'type' => 'success',
			'message' => "{$productName} removed from cart.",
		]);
	}
}
