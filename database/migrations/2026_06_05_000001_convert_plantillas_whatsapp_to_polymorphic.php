<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columna de tipo (nullable primero)
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->string('plantillable_type', 100)->nullable()->after('id_servicio');
        });

        // 2. Poblar registros existentes como servicios
        DB::table('plantillas_whatsapp')->update([
            'plantillable_type' => 'App\\Models\\servicios'
        ]);

        // 3. Hacer NOT NULL
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->string('plantillable_type', 100)->nullable(false)->change();
        });

        // 4. Eliminar FK
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropForeign('plantillas_whatsapp_id_servicio_foreign');
        });

        // 5. Eliminar unique compuesta original
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropUnique('unique_servicio_numero');
        });

        // 6. Eliminar index simple
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropIndex('plantillas_whatsapp_id_servicio_index');
        });

        // 7. Renombrar columna id_servicio → plantillable_id
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->renameColumn('id_servicio', 'plantillable_id');
        });

        // 8. Nueva unique compuesta (tipo + id + numero)
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->unique(
                ['plantillable_type', 'plantillable_id', 'numero_plantilla'],
                'unique_plantillable_numero'
            );
        });

        // 9. Índice compuesto para queries por owner
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->index(
                ['plantillable_type', 'plantillable_id'],
                'plantillas_whatsapp_plantillable_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropUnique('unique_plantillable_numero');
        });

        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropIndex('plantillas_whatsapp_plantillable_index');
        });

        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->renameColumn('plantillable_id', 'id_servicio');
        });

        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->unique(['id_servicio', 'numero_plantilla'], 'unique_servicio_numero');
            $table->index('id_servicio', 'plantillas_whatsapp_id_servicio_index');
        });

        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->foreign('id_servicio', 'plantillas_whatsapp_id_servicio_foreign')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');
        });

        Schema::table('plantillas_whatsapp', function (Blueprint $table) {
            $table->dropColumn('plantillable_type');
        });
    }
};
