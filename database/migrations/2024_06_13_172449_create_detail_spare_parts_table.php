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
        Schema::create('detail_spare_parts', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('movementType');
            $table->decimal('purchasePrice');
            $table->decimal('salePrice');
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('quotation_id')->constrained('quotations');
            $table->foreignId('spare_part_id')->constrained('spare_parts');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_spare_parts');
    }
};
