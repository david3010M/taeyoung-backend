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
        Schema::create('option_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('route')->unique();
            $table->integer('order');
            $table->string('icon');
            $table->foreignId('groupmenu_id')->constrained('group_menus');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_menus');
    }
};
