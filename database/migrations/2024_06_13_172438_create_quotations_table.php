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
//        COTIZACIONES
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->dateTime('date');
            $table->string('detail')->nullable();

            $table->string('paymentType')->nullable(); // CONTADO O CREDITO
            $table->string('currencyType')->nullable();
            $table->decimal('totalMachinery')->nullable();
            $table->decimal('totalSpareParts')->nullable();
            $table->decimal('subtotal')->nullable()->default(0);
            $table->decimal('igv')->nullable()->default(0);
            $table->decimal('discount')->nullable()->default(0);
            $table->decimal('total')->nullable()->default(0);

            $table->foreignId('client_id')->constrained('people');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
