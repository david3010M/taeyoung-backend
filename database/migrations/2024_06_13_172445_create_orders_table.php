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
            $table->string('number');
            $table->date('date');
            $table->string('detail')->nullable(); // TEXT LIBRE

            $table->string('type'); // machineryPurchase, machinerySale, sparePartPurchase, sparePartSale
            $table->string('documentType')->nullable(); // BOLETA, FACTURA, TICKET
            $table->string('paymentType')->nullable(); // PAGO A SUNAT
            $table->string('currencyType')->nullable(); // SOLES, DOLARES

            $table->decimal('totalMachinery')->nullable();
            $table->decimal('totalSpareParts')->nullable();

            $table->decimal('subtotal')->nullable()->default(0);
            $table->decimal('igv')->nullable()->default(0);
            $table->decimal('discount')->nullable()->default(0);
            $table->decimal('total')->nullable()->default(0);
            $table->decimal('totalIncome')->default(0);
            $table->decimal('totalExpense')->default(0);

            $table->string('comment')->nullable();
            $table->foreignId('supplier_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations');
            $table->foreignId('client_id')->nullable()->unsigned()->constrained('people');

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
