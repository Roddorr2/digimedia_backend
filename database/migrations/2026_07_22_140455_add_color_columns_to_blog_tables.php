<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->string('titulo_color')->nullable()->after('bg_colors');
            $table->string('texto_frase_color')->nullable()->after('titulo_color');
            $table->string('texto_descripcion_color')->nullable()->after('texto_frase_color');
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->string('titulo_color')->nullable()->after('bg_colors');
            $table->string('descripcion_color')->nullable()->after('titulo_color');
            $table->string('titulo_tarjeta_color')->nullable()->after('descripcion_color');
            $table->string('titulo_consejos_color')->nullable()->after('titulo_tarjeta_color');
        });

        Schema::table('consejos', function (Blueprint $table) {
            $table->string('texto_color')->nullable()->after('enlace');
        });

        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('titulo_color')->nullable()->after('palabra');
            $table->string('descripcion_color')->nullable()->after('titulo_color');
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->string('titulo_color')->nullable()->after('bg_colors');
            $table->string('descripcion_color')->nullable()->after('titulo_color');
        });
    }

    public function down(): void
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->dropColumn(['titulo_color', 'texto_frase_color', 'texto_descripcion_color']);
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->dropColumn(['titulo_color', 'descripcion_color', 'titulo_tarjeta_color', 'titulo_consejos_color']);
        });

        Schema::table('consejos', function (Blueprint $table) {
            $table->dropColumn('texto_color');
        });

        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn(['titulo_color', 'descripcion_color']);
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->dropColumn(['titulo_color', 'descripcion_color']);
        });
    }
};