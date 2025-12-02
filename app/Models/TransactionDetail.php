<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
	use HasFactory;

	protected $fillable = [
		'transaction_id',
		'product_id',
		'quantity',
		'price',
		'total',
	];

	protected function casts(): array
	{
		return [
			'price' => 'decimal:2',
			'total' => 'decimal:2',
			'quantity' => 'integer',
		];
	}

	public function transaction()
	{
		return $this->belongsTo(Transaction::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
