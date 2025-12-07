<?php

namespace App\Http\Controllers\Pos\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartUpdateController extends Controller
{
    /**
     * Update the quantity of a cart item.
     *
     * Validates that quantity is at least 1 and enforces authorization.
     */
    public function __invoke(Request $request, $userId, CartItem $cartItem)
    {
        // Authorization check
        if ($cartItem->user_id != $userId) {
            abort(403, 'Unauthorized to update this cart item.');
        }

        // Validate input
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update with validated quantity (ensure minimum 1)
        $newQuantity = max((int)$data['quantity'], 1);
        $cartItem->update(['quantity' => $newQuantity]);

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => 'Cart item quantity updated.',
        ]);
    }
}
