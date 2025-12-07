<?php

namespace App\Http\Controllers\Pos\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartClearController extends Controller
{
	/**
	 * Clear all items from the user's cart.
	 *
	 * Validates authorization and checks if cart is empty before clearing.
	 */
	public function __invoke(Request $request, $userId)
	{
		// Authorization check
		if ($request->user()->id != $userId) {
			abort(403, 'Unauthorized to clear this cart.');
		}

		// Check if cart has items
		$count = CartItem::where('user_id', $userId)->count();

		if ($count === 0) {
			return redirect()->back()->with('alert', [
				'type' => 'info',
				'message' => 'Cart is already empty.',
			]);
		}

		// Clear all items
		CartItem::where('user_id', $userId)->delete();

		return redirect()->back()->with('alert', [
			'type' => 'success',
			'message' => "Cart cleared. ({$count} items removed)",
		]);
	}
}
