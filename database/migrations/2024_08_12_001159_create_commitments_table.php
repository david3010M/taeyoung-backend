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
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->dateTime('date');

            $table->string('paymentType')->nullable(); // CHECK
            $table->string('currencyType')->nullable(); // CHECK

            $table->decimal('total')->nullable();
            $table->decimal('initialPayment')->nullable();
            $table->decimal('balance')->nullable();
            $table->integer('debts')->nullable();
            $table->string('periodPayment')->nullable();
            $table->decimal('exchangeRate')->nullable();

            $table->foreignId('currency_id')->nullable()->constrained();
            $table->foreignId('client_id')->nullable()->constrained('people');
            $table->foreignId('quotation_id')->nullable()->constrained();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitments');
    }
};
