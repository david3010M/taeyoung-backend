<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // client or provider
            $table->string('typeDocument');
            $table->string('dni')->nullable();
            $table->string('ruc')->nullable();
            $table->string('filterName')->nullable();
            $table->string('businessName')->nullable();
            $table->string('names')->nullable();
            $table->string('fatherSurname')->nullable();
            $table->string('motherSurname')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('representativeDni')->nullable();
            $table->string('representativeNames')->nullable();
            $table->foreignId('country_id')->nullable()->unsigned()->constrained('countries');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
