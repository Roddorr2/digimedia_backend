<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            // Campos para control de límite diario
            $table->integer('envios_hoy')->default(0)->after('envios_pendientes');
            $table->date('fecha_ultimo_envio')->nullable()->after('envios_hoy');
        });

        // Modificar ENUM para agregar nuevos estados
        DB::statement("ALTER TABLE campanias_whatsapp MODIFY COLUMN estado ENUM('borrador', 'pendiente', 'en_proceso', 'pausada_hasta_mañana', 'completada', 'cancelada', 'error') DEFAULT 'borrador'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir ENUM a valores originales
        DB::statement("ALTER TABLE campanias_whatsapp MODIFY COLUMN estado ENUM('pendiente', 'en_proceso', 'completada', 'cancelada', 'error') DEFAULT 'pendiente'");

        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            $table->dropColumn(['envios_hoy', 'fecha_ultimo_envio']);
        });
    }
};
