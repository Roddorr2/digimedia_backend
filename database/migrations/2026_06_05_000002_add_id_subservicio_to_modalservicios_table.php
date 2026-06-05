<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_subservicio')
                ->nullable()
                ->after('id_servicio')
                ->comment('Subservicio desde cuyo pop-up se registró el lead');

            $table->index('id_subservicio', 'modalservicios_id_subservicio_index');
        });
    }

    public function down(): void
    {
        Schema::table('modalservicios', function (Blueprint $table) {
            $table->dropIndex('modalservicios_id_subservicio_index');
            $table->dropColumn('id_subservicio');
        });
    }
};
