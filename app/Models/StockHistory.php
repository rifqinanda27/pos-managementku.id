<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockHistory extends Model
{
	use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<string>
	 */
	protected $fillable = [
		'product_id',
		'user_id',
		'type',
		'quantity',
		'notes',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'quantity' => 'integer',
		];
	}

	/**
	 * Get the product that owns the stock history.
	 */
	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	/**
	 * Get the user that owns the stock history.
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
