<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->string('plantillable_type', 100)->nullable()->after('id_servicio');
        });

        DB::table('plantillas_email')->update([
            'plantillable_type' => 'App\\Models\\servicios'
        ]);

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->string('plantillable_type', 100)->nullable(false)->change();
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropForeign('plantillas_email_id_servicio_foreign');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropUnique('unique_servicio_numero');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropIndex('plantillas_email_id_servicio_index');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->renameColumn('id_servicio', 'plantillable_id');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->unique(
                ['plantillable_type', 'plantillable_id', 'numero_plantilla'],
                'unique_email_plantillable_numero'
            );
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->index(
                ['plantillable_type', 'plantillable_id'],
                'plantillas_email_plantillable_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropUnique('unique_email_plantillable_numero');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropIndex('plantillas_email_plantillable_index');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->renameColumn('plantillable_id', 'id_servicio');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->unique(['id_servicio', 'numero_plantilla'], 'unique_servicio_numero');
            $table->index('id_servicio', 'plantillas_email_id_servicio_index');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->foreign('id_servicio', 'plantillas_email_id_servicio_foreign')
                ->references('id_servicio')
                ->on('servicios')
                ->onDelete('cascade');
        });

        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropColumn('plantillable_type');
        });
    }
};
