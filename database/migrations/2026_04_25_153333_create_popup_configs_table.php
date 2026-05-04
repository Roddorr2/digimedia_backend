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
        Schema::create('popup_configs', function (Blueprint $table) {
            $table->id('id_popup_config');
            
            $table->unsignedBigInteger('id_subservicio')->unique();
            $table->foreign('id_subservicio')
                  ->references('id_subservicio')
                  ->on('subservicios')
                  ->onDelete('cascade');

            $table->string('title_text', 80);
            $table->string('title_color', 7)->default('#FFFFFF');
            $table->string('button_text', 25);
            $table->string('button_color', 7)->default('#7C3FD9');
            $table->string('service_color', 7);
            $table->string('service_color_2', 7)->nullable();
            $table->string('gradient_direction', 20)->default('to bottom');

            $table->unsignedTinyInteger('trigger_time');

            $table->string('left_image_url', 500)->nullable();
            $table->unsignedTinyInteger('left_opacity')->default(100);
            $table->string('left_alt', 255)->nullable();
            
            $table->string('right_image_url', 500)->nullable();
            $table->unsignedTinyInteger('right_opacity')->default(100);
            $table->string('right_alt', 255)->nullable();

            $table->string('mobile_image_url', 500)->nullable();
            $table->unsignedTinyInteger('mobile_opacity')->default(100);
            $table->string('mobile_alt', 255)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_configs');
    }
};
