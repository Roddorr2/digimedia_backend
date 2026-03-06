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
        Schema::create('contactanos', function (Blueprint $table) {
            $table->id('id_contactanos');
            $table->string('nombre', 250);
            $table->string('email', 250);
            $table->string('numero', 15);
            $table->text('mensaje')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactanos');
    }
};