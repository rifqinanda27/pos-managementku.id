<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\ProductManagementStoreRequest;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Auth;

class ProductManagementStoreController extends Controller
{
    /**
     * Store a newly created product in storage.
     */
    public function __invoke(ProductManagementStoreRequest $request)
    {
        try {
            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku ?: null, // Will auto-generate if null
                'current_stock' => $request->starting_stock ?: 0,
            ]);

            // Create stock history if starting stock > 0
            if ($request->starting_stock && $request->starting_stock > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'increase',
                    'quantity' => $request->starting_stock,
                    'notes' => 'Initial stock',
                ]);
            }

            return redirect()
                ->route('product-management.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }
}
