<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductManagement\ProductManagementUpdateRequest;
use App\Models\Product;

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
			]);

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
