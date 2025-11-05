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
        Schema::table('commend_tarjetas', function (Blueprint $table) {
            $table->string('titulo', 255)->nullable()->change();
            $table->string('texto1', 255)->nullable()->change();
            $table->string('texto2', 255)->nullable()->change();
            $table->string('texto3', 255)->nullable()->change();
            $table->string('texto4', 255)->nullable()->change();
            $table->string('texto5', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commend_tarjetas', function (Blueprint $table) {
            $table->string('titulo', 191)->nullable()->change();
            $table->string('texto1', 191)->nullable()->change();
            $table->string('texto2', 191)->nullable()->change();
            $table->string('texto3', 191)->nullable()->change();
            $table->string('texto4', 191)->nullable()->change();
            $table->string('texto5', 191)->nullable()->change();
        });
    }
};
