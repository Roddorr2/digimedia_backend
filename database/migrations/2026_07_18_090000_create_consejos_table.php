<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consejos', function (Blueprint $table) {
            $table->id('id_consejo');
            $table->text('texto');
            $table->string('palabra')->nullable();
            $table->string('enlace')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->foreignId('id_blog_body')->references('id_blog_body')->on('blog_bodies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consejos');
    }
};
