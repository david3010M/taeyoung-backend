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
        Schema::create('account_payables', function (Blueprint $table) {
            $table->id();
            $table->string('paymentType')->nullable();
            $table->integer('days');
            $table->date('date');
            $table->decimal('amount');
            $table->decimal('balance');
            $table->string('status')->default('PENDIENTE');
            $table->foreignId('supplier_id')->constrained('people');
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->foreignId('order_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payables');
    }
};
