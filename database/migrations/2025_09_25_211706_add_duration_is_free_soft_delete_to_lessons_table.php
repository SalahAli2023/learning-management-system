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
        Schema::table('lessons', function (Blueprint $table) {
             // Add duration column in minutes
            $table->integer('duration')->nullable()->after('file_url');

            // Add free lesson flag
            $table->boolean('is_free')->default(false)->after('duration');

            // Add Soft Deletes
            $table->softDeletes();
            
            // Add indexes for better performance
            $table->index(['is_free', 'duration']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['is_free', 'duration']);
            $table->dropIndex(['deleted_at']);
            
            // Remove columns
            $table->dropColumn('duration');
            $table->dropColumn('is_free');
            $table->dropSoftDeletes();
        });
    }
};
