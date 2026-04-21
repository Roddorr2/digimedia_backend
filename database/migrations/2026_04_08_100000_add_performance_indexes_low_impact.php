<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ÍNDICES DE BAJO IMPACTO - Performance (A)
     * Estos índices mejoran búsquedas sin afectar escrituras
     */
    public function up(): void
    {
        // Índice FULLTEXT para búsquedas de empleados por nombre/apellido
        // Permite búsquedas rápidas tipo: WHERE MATCH(nombre, apellido) AGAINST(...)
        Schema::table('empleados', function (Blueprint $table) {
            $table->fullText(['nombre', 'apellido'])->change();
        });

        // Índice compuesto para filtros combinados en modalservicios
        // Optimiza queries: WHERE id_servicio = ? AND estado = ?
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->index(['id_servicio', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover FULLTEXT de empleados
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropFullText(['nombre', 'apellido']);
        });

        // Remover índice compuesto de modalservicios
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->dropIndex(['id_servicio', 'estado']);
        });
    }
};
