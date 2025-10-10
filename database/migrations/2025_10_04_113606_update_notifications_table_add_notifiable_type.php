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
        Schema::table('notifications', function (Blueprint $table) {
            // نضيف الحقول اللي ناقصة
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->after('id');
            }
            
            if (!Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');
            }
            
            // نضيف index للتحسين
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
    }
};
