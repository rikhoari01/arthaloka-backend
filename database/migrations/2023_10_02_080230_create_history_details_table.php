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
        Schema::create('history_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_header_id');
            $table->bigInteger('casette_1');
            $table->bigInteger('casette_2');
            $table->bigInteger('casette_3');
            $table->bigInteger('casette_4');
            $table->bigInteger('casette_5');
            $table->bigInteger('casette_6');
            $table->bigInteger('casette_7');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_details');
    }
};
