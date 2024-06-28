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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // machineryPurchase, machinerySale, sparePartPurchase, sparePartSale
            $table->dateTime('date');
            $table->string('number');
            $table->string('documentType'); // BOLETA, FACTURA, TICKET
            $table->string('detail'); // TEXT LIBRE
            $table->decimal('totalIncome');
            $table->decimal('totalExpense');
            $table->string('currency');
            $table->string('typePayment'); // PAGO A SUNAT
            $table->string('comment')->nullable();
            $table->foreignId('supplier_id')->nullable()->unsigned()->constrained('people');
//            COTIZATION
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
