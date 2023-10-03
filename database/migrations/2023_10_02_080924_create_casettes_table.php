<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('casettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atm_id');
            $table->bigInteger('casette_1')->default(1000);
            $table->bigInteger('casette_2')->default(1000);
            $table->bigInteger('casette_3')->default(1000);
            $table->bigInteger('casette_4')->default(1000);
            $table->bigInteger('casette_5')->default(1000);
            $table->bigInteger('casette_6')->default(1000);
            $table->bigInteger('casette_7')->default(1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casettes');
    }
};
