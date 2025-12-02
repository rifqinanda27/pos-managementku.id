<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'total',
	];

	protected function casts(): array
	{
		return [
			'total' => 'decimal:2',
		];
	}

	public function details()
	{
		return $this->hasMany(TransactionDetail::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
