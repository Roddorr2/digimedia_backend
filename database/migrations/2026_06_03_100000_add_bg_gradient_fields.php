<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->string('bg_type', 20)->default('solid')->after('bg_color');
            $table->string('bg_colors', 50)->nullable()->after('bg_type');
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->string('bg_type', 20)->default('solid')->after('bg_color');
            $table->string('bg_colors', 50)->nullable()->after('bg_type');
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->string('bg_type', 20)->default('solid')->after('bg_color');
            $table->string('bg_colors', 50)->nullable()->after('bg_type');
        });
    }

    public function down()
    {
        Schema::table('blog_heads', function (Blueprint $table) {
            $table->dropColumn(['bg_type', 'bg_colors']);
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->dropColumn(['bg_type', 'bg_colors']);
        });

        Schema::table('blog_footers', function (Blueprint $table) {
            $table->dropColumn(['bg_type', 'bg_colors']);
        });
    }
};