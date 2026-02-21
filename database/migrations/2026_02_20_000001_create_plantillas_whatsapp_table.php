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
        Schema::create('plantillas_whatsapp', function (Blueprint $table) {
            // Identificadores
            $table->id('id_plantilla_whatsapp');
            $table->foreignId('id_servicio')
                ->constrained('servicios', 'id_servicio')
                ->onDelete('cascade');
            $table->tinyInteger('numero_plantilla')
                ->unsigned()
                ->comment('Número de plantilla: 1, 2 o 3');
            
            // Contenido
            $table->string('nombre', 100)
                ->nullable()
                ->comment('Nombre descriptivo opcional');
            $table->text('mensaje')
                ->comment('Mensaje de WhatsApp con placeholders como {nombre}');
            $table->string('imagen_url', 500)
                ->comment('URL de la imagen (local o Cloudinary)');
            
            // Auditoría
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null')
                ->comment('Usuario que creó la plantilla');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null')
                ->comment('Último usuario que modificó');
            $table->timestamps();
            
            // Índices y restricciones
            $table->unique(['id_servicio', 'numero_plantilla'], 'unique_servicio_numero');
            $table->index('id_servicio');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_whatsapp');
    }
};
