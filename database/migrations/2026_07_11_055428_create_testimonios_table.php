<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
       Schema::create('testimonios', function (Blueprint $table) {
            $table->id('id_testimonio');
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->text('texto');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('imagen_public_id')->nullable();
            $table->string('imagen_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonios');
    }
};
