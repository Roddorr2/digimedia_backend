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
        Schema::table('popup_configs', function (Blueprint $table) {
            if (Schema::hasColumn('popup_configs', 'title_text')) {
                $table->dropColumn('title_text');
            }
            if (Schema::hasColumn('popup_configs', 'title_color')) {
                $table->dropColumn('title_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->string('title_text', 80)->nullable()->after('id_subservicio');
            $table->string('title_color', 7)->default('#FFFFFF')->after('title_text');
        });
    }
};
