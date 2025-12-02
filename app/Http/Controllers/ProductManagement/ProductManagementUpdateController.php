<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\ProductManagementUpdateRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductManagementUpdateController extends Controller
{
    /**
     * Update the specified product in storage.
     */
    public function __invoke(ProductManagementUpdateRequest $request, Product $product)
    {
        try {
            $product->update([
                'name' => $request->name,
                'sku' => $request->sku ?: $product->sku,
                'price' => $request->price,
                'description' => $request->description ?: null,
            ]);

            // handle new image upload and remove old image
            if ($request->hasFile('image')) {
                // delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;
                $product->save();
            }

            return redirect()
                ->route('product-management.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }
}
