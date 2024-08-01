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
            $table->string('detail');
            $table->dateTime('date');
            $table->string('currencyType')->nullable();
            $table->string('price')->nullable();
            $table->string('initialPayment')->nullable();
            $table->string('balance')->nullable();
            $table->string('debts')->nullable();
            $table->string('exchangeRate')->nullable();

            $table->foreignId('currency_id')->nullable()->constrained();
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
