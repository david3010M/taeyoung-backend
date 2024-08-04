<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_machineries', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->integer('quantity');
            $table->string('movementType')->default('purchase');
            $table->decimal('purchasePrice')->nullable();
            $table->decimal('salePrice')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_machineries');
    }
};
