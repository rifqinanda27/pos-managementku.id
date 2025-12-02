<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductManagementDeleteController extends Controller
{
    /**
     * Remove the specified product from storage.
     */
    public function __invoke(Product $product)
    {
        try {
            $product->delete();

            return redirect()
                ->route('product-management.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
