<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->foreignId('id_subservicio')
                  ->nullable()
                  ->after('id_servicio')
                  ->references('id_subservicio')
                  ->on('subservicios')
                  ->onDelete('set null');

            $table->index('id_subservicio');
        });
    }

    public function down(): void
    {
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->dropForeign(['id_subservicio']);
            $table->dropIndex(['id_subservicio']);
            $table->dropColumn('id_subservicio');
        });
    }
};