<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('positive_feedbacks_count')->default(0);
            $table->unsignedInteger('negative_feedbacks_count')->default(0);
            $table->float('rating')->index()->default(0);
        });

        // Для users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('positive_feedbacks_count')->default(0);
            $table->unsignedInteger('negative_feedbacks_count')->default(0);
            $table->float('seller_rating')->index()->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('seller_rating');
            $table->dropColumn('negative_feedbacks_count');
            $table->dropColumn('positive_feedbacks_count');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('rating');
            $table->dropColumn('negative_feedbacks_count');
            $table->dropColumn('positive_feedbacks_count');
        });
    }
};
