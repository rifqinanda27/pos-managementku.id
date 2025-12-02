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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'price')) {
            Schema::table('products', function (Blueprint $table) {
                // Price used by POS (stored as decimal with 2 fractional digits)
                $table->decimal('price', 15, 2)->default(0.00)->after('sku');
                // Rich text description (HTML) for product details
                $table->text('description')->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'description')) {
                    $table->dropColumn('description');
                }

                if (Schema::hasColumn('products', 'price')) {
                    $table->dropColumn('price');
                }
            });
        }
    }
};
