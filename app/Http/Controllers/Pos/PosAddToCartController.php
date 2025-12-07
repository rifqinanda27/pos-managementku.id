<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosAddToCartController extends Controller
{
	/**
	 * Add item to cart for the current authenticated user.
	 */
	public function __invoke(Request $request)
	{
		$data = $request->validate([
			'product_id' => 'required|exists:products,id',
			'quantity' => 'required|integer|min:1',
		]);

		$user = $request->user();
		$product = Product::findOrFail($data['product_id']);

		// Get or create cart item
		$cartItem = CartItem::firstOrNew([
			'user_id' => $user->id,
			'product_id' => $product->id,
		]);

		// If item exists, add to quantity; if new, set to desired quantity
		$newQuantity = $cartItem->exists ? $cartItem->quantity + $data['quantity'] : $data['quantity'];

		// Ensure quantity is at least 1
		$newQuantity = max($newQuantity, 1);

		$cartItem->quantity = $newQuantity;
		$cartItem->price = $product->price;
		$cartItem->save();

		return redirect()->back()->with('alert', [
			'type' => 'success',
			'message' => 'Product added to cart.',
		]);
	}
}
