<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosTerminalController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->whereNull('deleted_at');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('pos/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Add item to cart for the current authenticated user.
     */
    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $product = Product::findOrFail($data['product_id']);

        $cart = CartItem::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['quantity' => DB::raw('GREATEST(quantity + ' . ((int)$data['quantity']) . ', 1)'), 'price' => $product->price]
        );

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    /**
     * Checkout a single product immediately (create transaction for that product).
     */
    public function checkoutSingle(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $product = Product::lockForUpdate()->findOrFail($data['product_id']);

        if ($product->current_stock < $data['quantity']) {
            return redirect()->back()->with('error', 'Not enough stock for this product.');
        }

        DB::beginTransaction();
        try {
            $total = bcmul((string)$product->price, (string)$data['quantity'], 2);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'total' => $total,
            ]);

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'price' => $product->price,
                'total' => $total,
            ]);

            // Update product stock and total_sold
            $product->current_stock = $product->current_stock - $data['quantity'];
            $product->total_sold = $product->total_sold + $data['quantity'];
            $product->save();

            DB::commit();

            return redirect('/pos-terminal')->with('success', 'Checkout successful.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed.');
        }
    }
}
