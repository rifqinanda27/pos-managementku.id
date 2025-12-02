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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super-admin', 'admin', 'cashier'])->default('cashier')->after('password');
                $table->index('role');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Check if constraint exists and drop it
        $constraintExists = DB::select("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'users' AND constraint_name = 'users_email_unique'");

        if (!empty($constraintExists)) {
            DB::statement('ALTER TABLE users DROP CONSTRAINT users_email_unique');
        }

        // Add partial unique index for email (unique only when deleted_at IS NULL)
        $partialIndexExists = DB::select("SELECT indexname FROM pg_indexes WHERE indexname = 'unique_email_not_deleted'");
        if (empty($partialIndexExists)) {
            DB::statement('CREATE UNIQUE INDEX unique_email_not_deleted ON users(email) WHERE deleted_at IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial unique index
        DB::statement('DROP INDEX IF EXISTS unique_email_not_deleted');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropSoftDeletes();
            // Restore the original unique constraint
            $table->unique('email');
        });
    }
};
