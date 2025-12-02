<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		if (!Schema::hasTable('products')) {
			Schema::create('products', function (Blueprint $table) {
				$table->id();
				$table->string('name');
				$table->string('sku');
				$table->integer('current_stock')->default(0)->unsigned();
				$table->integer('total_sold')->default(0)->unsigned();
				$table->timestamps();
				$table->softDeletes();

				$table->index('name');
			});

			// Add partial unique index for sku (unique only when deleted_at IS NULL)
			DB::statement('CREATE UNIQUE INDEX unique_sku_not_deleted ON products(sku) WHERE deleted_at IS NULL');
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		DB::statement('DROP INDEX IF EXISTS unique_sku_not_deleted');
		Schema::dropIfExists('products');
	}
};
