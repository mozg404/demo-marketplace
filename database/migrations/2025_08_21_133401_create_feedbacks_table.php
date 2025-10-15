<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_positive');
            $table->string('comment', 255)->nullable();
            $table->timestamps();

            $table->unique(['order_item_id']);

            $table->index('user_id');
            $table->index('product_id');
            $table->index('seller_id');
            $table->index(['seller_id', 'is_positive']);
            $table->index(['product_id', 'is_positive']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
