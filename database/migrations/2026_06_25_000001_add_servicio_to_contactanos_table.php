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
        Schema::table('contactanos', function (Blueprint $table) {
            $table->string('servicio', 100)->nullable()->after('mensaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contactanos', function (Blueprint $table) {
            $table->dropColumn('servicio');
        });
    }
};