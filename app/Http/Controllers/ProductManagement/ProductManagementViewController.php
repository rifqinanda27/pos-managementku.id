<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductManagementViewController extends Controller
{
	/**
	 * Display a listing of products.
	 */
	public function index(Request $request): Response
	{
		$query = Product::query();

		// Search functionality
		if ($request->has('search') && $request->search) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('sku', 'like', "%{$search}%");
			});
		}

		$perPage = $request->get('per_page', 10);
		$products = $query->latest()->paginate($perPage)->withQueryString();

		return Inertia::render('product-management/Index', [
			'products' => $products,
			'filters' => $request->only(['search']),
		]);
	}

	/**
	 * Show the form for creating a new product.
	 */
	public function create(): Response
	{
		return Inertia::render('product-management/Create');
	}

	/**
	 * Show the form for editing the specified product.
	 */
	public function edit(Product $product): Response
	{
		return Inertia::render('product-management/Edit', [
			'product' => $product,
		]);
	}
}
