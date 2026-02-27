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
        Schema::table('modal_wats', function (Blueprint $table) {
            // FASE 3: Campos para retry logic
            $table->unsignedTinyInteger('intentos')->default(1)->after('number_message');
            $table->unsignedBigInteger('campania_id')->nullable()->after('intentos');
            $table->boolean('puede_reintentar')->default(true)->after('campania_id');
            
            // Índice para optimizar búsqueda de fallidos
            $table->index(['campania_id', 'estado', 'puede_reintentar']);
            
            // Foreign key a campanias_whatsapp
            $table->foreign('campania_id')
                  ->references('id_campania')
                  ->on('campanias_whatsapp')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modal_wats', function (Blueprint $table) {
            $table->dropForeign(['campania_id']);
            $table->dropIndex(['campania_id', 'estado', 'puede_reintentar']);
            $table->dropColumn(['intentos', 'campania_id', 'puede_reintentar']);
        });
    }
};
