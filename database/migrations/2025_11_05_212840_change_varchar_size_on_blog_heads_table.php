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
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->string('titulo', 255)->change();
            $table->string('texto_frase', 255)->change();
            $table->string('texto_descripcion', 255)->change();
            $table->string('alt', 255)->nullable()->change();
            $table->string('title', 255)->nullable()->change();
            $table->string('meta_title', 255)->nullable()->change();
            $table->string('meta_descripcion', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->string('titulo', 50)->change();
            $table->string('texto_frase', 70)->change();
            $table->string('texto_descripcion', 120)->change();
            $table->string('alt', 191)->nullable()->change();
            $table->string('title', 191)->nullable()->change();
            $table->string('meta_title', 191)->nullable()->change();
            $table->string('meta_descripcion', 191)->nullable()->change();
        });
    }
};