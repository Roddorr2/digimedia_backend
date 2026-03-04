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
        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            // Agregar user_id para auditoría - quién creó la campaña
            $table->unsignedBigInteger('user_id')->nullable()->after('id_servicio');
            
            // Crear foreign key con users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Si se elimina el usuario, mantener el registro
            
            // Índice para consultas por usuario
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
