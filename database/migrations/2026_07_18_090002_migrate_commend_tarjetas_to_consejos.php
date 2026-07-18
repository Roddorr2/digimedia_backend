<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $blogBodies = DB::table('blog_bodies')->whereNotNull('id_commend_tarjeta')->get();

        foreach ($blogBodies as $blogBody) {
            $commendTarjeta = DB::table('commend_tarjetas')
                ->where('id_commend_tarjeta', $blogBody->id_commend_tarjeta)
                ->first();

            if (!$commendTarjeta) {
                continue;
            }

            if (!empty($commendTarjeta->titulo)) {
                DB::table('blog_bodies')
                    ->where('id_blog_body', $blogBody->id_blog_body)
                    ->update(['titulo_consejos' => $commendTarjeta->titulo]);
            }

            $orden = 0;
            foreach (['texto1', 'texto2', 'texto3', 'texto4', 'texto5'] as $campo) {
                $texto = $commendTarjeta->{$campo} ?? null;
                if (empty($texto)) {
                    continue;
                }

                DB::table('consejos')->insert([
                    'texto' => $texto,
                    'palabra' => $commendTarjeta->palabra,
                    'enlace' => $commendTarjeta->enlace,
                    'orden' => $orden,
                    'id_blog_body' => $blogBody->id_blog_body,
                ]);

                $orden++;
            }
        }

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->dropForeign(['id_commend_tarjeta']);
            $table->dropColumn('id_commend_tarjeta');
        });

        Schema::dropIfExists('commend_tarjetas');
    }

    public function down(): void
    {
        Schema::create('commend_tarjetas', function (Blueprint $table) {
            $table->id('id_commend_tarjeta');
            $table->string('titulo')->nullable();
            $table->string('texto1')->nullable();
            $table->string('texto2')->nullable();
            $table->string('texto3')->nullable();
            $table->string('texto4')->nullable();
            $table->string('texto5')->nullable();
            $table->string('palabra')->nullable();
            $table->string('enlace')->nullable();
        });

        Schema::table('blog_bodies', function (Blueprint $table) {
            $table->foreignId('id_commend_tarjeta')->unique()->nullable()->after('descripcion')
                ->references('id_commend_tarjeta')->on('commend_tarjetas')->onDelete('cascade');
        });

        DB::table('consejos')->delete();
    }
};
