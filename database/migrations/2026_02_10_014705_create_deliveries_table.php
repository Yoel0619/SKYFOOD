<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('delivery_person_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('delivery_address');
            $table->string('phone');
            $table->enum('status', ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->text('delivery_notes')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->string('tracking_number')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};