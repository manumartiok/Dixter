<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Industrias: "destacado" decide cuáles se muestran en el Home.
 * La página Aplicaciones muestra todas las activas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sectores', function (Blueprint $table) {
            $table->boolean('destacado')->default(false)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('sectores', function (Blueprint $table) {
            $table->dropColumn('destacado');
        });
    }
};
