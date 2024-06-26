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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // client or provider
            $table->string('ruc'); // UNIQUE THEN
            $table->string('businessName')->nullable();
//            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->integer('phone')->nullable();
            $table->string('representativeDni')->nullable();
            $table->string('representativeNames')->nullable();
            $table->foreignId('country_id')->nullable()->unsigned()->constrained('countries');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
