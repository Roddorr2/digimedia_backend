<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo crear triggers en MySQL, SQLite no los soporta
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared(
                "
                CREATE TRIGGER after_update_usuarios
                AFTER UPDATE ON users
                FOR EACH ROW
                BEGIN
                    INSERT INTO user_log (tabla_afectada, operacion, usuario_sql, detalle)
                    VALUES (
                        'usuarios',
                        'UPDATE',
                        SUBSTRING_INDEX(USER(), '@', 1),
                        CONCAT('ID: ', OLD.id, ', nombre cambiado de \"', OLD.name, '\" a \"', NEW.name, '\"')
                    );
                END;
            "
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_update_usuarios;");
    }
};
