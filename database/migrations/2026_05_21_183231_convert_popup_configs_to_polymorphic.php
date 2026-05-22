<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columna de tipo (nullable primero)
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->string('popupable_type', 100)->nullable()->after('id_subservicio');
        });
        
        // 2. Poblar los registros existentes como Subservicio
        DB::table('popup_configs')->update([
            'popupable_type' => 'App\\Models\\Subservicio'
        ]);
        
        // 3. Hacer NOT NULL la columna
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->string('popupable_type', 100)->nullable(false)->change();
        });
        
        // 4. Eliminar la FOREIGN KEY primero
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->dropForeign('popup_configs_id_subservicio_foreign');
        });
        
        // 5. Ahora sí, eliminar el UNIQUE index
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->dropUnique('popup_configs_id_subservicio_unique');
        });
        
        // 6. Renombrar la columna
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->renameColumn('id_subservicio', 'popupable_id');
        });
        
        // 7. Agregar nueva UNIQUE key compuesta
        Schema::table('popup_configs', function (Blueprint $table) {
            $table->unique(['popupable_type', 'popupable_id'], 'popup_configs_popupable_unique');
        });
    }

    public function down(): void
    {
        Schema::table('popup_configs', function (Blueprint $table) {
            // 1. Eliminar la unique compuesta
            $table->dropUnique('popup_configs_popupable_unique');
            
            // 2. Renombrar columna de vuelta
            $table->renameColumn('popupable_id', 'id_subservicio');
            
            // 3. Restaurar UNIQUE key
            $table->unique('id_subservicio', 'popup_configs_id_subservicio_unique');
            
            // 4. Restaurar FOREIGN KEY
            $table->foreign('id_subservicio', 'popup_configs_id_subservicio_foreign')
                  ->references('id_subservicio')
                  ->on('subservicios')
                  ->onDelete('cascade');
            
            // 5. Eliminar columna de tipo
            $table->dropColumn('popupable_type');
        });
    }
};