<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
	use HasFactory, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<string>
	 */
	protected $fillable = [
		'name',
		'sku',
		'current_stock',
		'total_sold',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'current_stock' => 'integer',
			'total_sold' => 'integer',
		];
	}

	/**
	 * Boot the model.
	 */
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($product) {
			if (empty($product->sku)) {
				$product->sku = self::generateUniqueSku();
			}
		});
	}

	/**
	 * Generate a unique SKU.
	 */
	public static function generateUniqueSku(): string
	{
		do {
			$sku = 'SKU-' . strtoupper(Str::random(8));
		} while (self::where('sku', $sku)->exists());

		return $sku;
	}

	/**
	 * Get the stock histories for the product.
	 */
	public function stockHistories()
	{
		return $this->hasMany(StockHistory::class);
	}
}
