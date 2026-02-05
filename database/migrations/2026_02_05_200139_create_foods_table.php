<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('image')->nullable();
            $table->integer('preparation_time')->default(20)->comment('in minutes');
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_available')->default(true);
            $table->integer('calories')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('allergen_info')->nullable();
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('slug');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};