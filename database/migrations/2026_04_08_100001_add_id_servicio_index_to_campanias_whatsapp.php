<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agregar índice en id_servicio para campanias_whatsapp
     * Optimiza queries: WHERE id_servicio = ?
     */
    public function up(): void
    {
        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            $table->index('id_servicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campanias_whatsapp', function (Blueprint $table) {
            $table->dropIndex(['id_servicio']);
        });
    }
};
