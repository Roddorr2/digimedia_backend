<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->string('color', 7)->default('#8a2be2')->after('encabezado');
        });
    }

    public function down(): void
    {
        Schema::table('plantillas_email', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
