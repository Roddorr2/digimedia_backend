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
        Schema::create('plantillas_email', function (Blueprint $table) {
            // Identificadores
            $table->id('id_plantilla_email');
            $table->foreignId('id_servicio')
                ->constrained('servicios', 'id_servicio')
                ->onDelete('cascade');
            $table->tinyInteger('numero_plantilla')
                ->unsigned()
                ->comment('Número de plantilla: 1, 2 o 3');
            
            // Contenido del email
            $table->string('nombre', 100)
                ->nullable()
                ->comment('Nombre descriptivo opcional');
            $table->string('asunto', 255)
                ->comment('Subject del email');
            $table->string('encabezado', 255)
                ->comment('Título principal del email');
            $table->string('imagen_url', 500)
                ->comment('URL de la imagen destacada');
            $table->text('mensaje')
                ->comment('Cuerpo del mensaje (puede contener HTML)');
            $table->string('mensaje_boton', 150)
                ->nullable()
                ->comment('Texto del botón de llamada a la acción');
            $table->string('url_boton', 500)
                ->nullable()
                ->comment('URL del botón CTA');
            $table->text('footer')
                ->nullable()
                ->comment('Pie de página del email');
            
            // Redes sociales (URLs)
            $table->string('red_facebook', 255)
                ->nullable()
                ->default('https://www.facebook.com/DigiMedia.Marketing1');
            $table->string('red_tiktok', 255)
                ->nullable();
            $table->string('red_instagram', 255)
                ->nullable()
                ->default('https://www.instagram.com/digimedia.pe/');
            $table->string('red_linkedin', 255)
                ->nullable();
            
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
        Schema::dropIfExists('plantillas_email');
    }
};
