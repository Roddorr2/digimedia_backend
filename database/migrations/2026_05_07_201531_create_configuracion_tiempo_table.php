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
        Schema::create('configuracion_tiempo', function (Blueprint $table) {
            $table->id('id_configuracion');
            $table->foreignId('id_servicio')
                ->constrained('servicios', 'id_servicio')
                ->onDelete('cascade');
            $table->enum('tipo', ['email', 'whatsapp']);
            $table->tinyInteger('numero_mensaje')->unsigned();
            $table->enum('unidad_tiempo', ['minutos', 'horas', 'dias'])->default('minutos');
            $table->integer('valor_tiempo')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['id_servicio', 'tipo', 'numero_mensaje'], 'unique_servicio_tipo_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_tiempo');
    }
};
