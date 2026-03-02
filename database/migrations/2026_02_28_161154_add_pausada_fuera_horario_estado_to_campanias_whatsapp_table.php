<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega el estado 'pausada_fuera_horario' para Ventana Horaria
     */
    public function up(): void
    {
        // Modificar el ENUM para incluir 'pausada_fuera_horario'
        DB::statement("ALTER TABLE campanias_whatsapp MODIFY COLUMN estado ENUM('borrador', 'pendiente', 'en_proceso', 'pausada_hasta_mañana', 'pausada_fuera_horario', 'completada', 'cancelada', 'error') DEFAULT 'borrador'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volver al ENUM anterior (sin 'pausada_fuera_horario')
        DB::statement("ALTER TABLE campanias_whatsapp MODIFY COLUMN estado ENUM('borrador', 'pendiente', 'en_proceso', 'pausada_hasta_mañana', 'completada', 'cancelada', 'error') DEFAULT 'borrador'");
    }
};
