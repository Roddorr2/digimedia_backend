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
        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->string('titulo', 255)->change();
            $table->string('alt_image1', 255)->nullable()->change();
            $table->string('title_image1', 255)->nullable()->change();
            $table->string('alt_image2', 255)->nullable()->change();
            $table->string('title_image2', 255)->nullable()->change();
            $table->string('alt_image3', 255)->nullable()->change();
            $table->string('title_image3', 255)->nullable()->change();
            $table->string('service_url', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->string('titulo', 191)->change();
            $table->string('alt_image1', 191)->nullable()->change();
            $table->string('title_image1', 191)->nullable()->change();
            $table->string('alt_image2', 191)->nullable()->change();
            $table->string('title_image2', 191)->nullable()->change();
            $table->string('alt_image3', 191)->nullable()->change();
            $table->string('title_image3', 191)->nullable()->change();
        });
    }
};