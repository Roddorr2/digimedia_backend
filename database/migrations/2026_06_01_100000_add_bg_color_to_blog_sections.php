<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->string('bg_color', 7)->nullable()->default('#1E40AF')->after('meta_descripcion');
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->string('bg_color', 7)->nullable()->default('#5A37A6')->after('titulo_tarjeta');
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->string('bg_color', 7)->nullable()->default('#374151')->after('enlace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->dropColumn('bg_color');
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->dropColumn('bg_color');
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->dropColumn('bg_color');
        });
    }
};