<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->dateTime('paymentDate')->nullable();
            $table->decimal('total')->nullable();
            $table->decimal('cash')->nullable();
            $table->decimal('yape')->nullable();
            $table->decimal('plin')->nullable();
            $table->decimal('card')->nullable();
            $table->decimal('deposit')->nullable();

            $table->string('typeDocument')->nullable(); // FACTURA | BOLETA
            $table->boolean('isBankPayment')->nullable()->default(false);
            $table->string('numberVoucher')->nullable();
            $table->string('routeVoucher')->nullable();
            $table->string('comment')->nullable();
            $table->string('status')->nullable()->default('GENERADA');

//            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('bank_id')->nullable()->unsigned()->constrained('banks');
            $table->foreignId('paymentConcept_id')->nullable()->unsigned()->constrained('payment_concepts');
            $table->foreignId('order_id')->nullable()->unsigned()->constrained('orders');
            $table->foreignId('accountReceivable_id')->nullable()->unsigned()->constrained('account_receivables');
            $table->foreignId('accountPayable_id')->nullable()->unsigned()->constrained('account_payables');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
