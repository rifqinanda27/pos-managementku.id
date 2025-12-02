<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('stock_histories')) {
            Schema::create('stock_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
                $table->enum('type', ['increase', 'decrease']);
                $table->integer('quantity')->unsigned();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('product_id');
                $table->index('user_id');
                $table->index('type');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
