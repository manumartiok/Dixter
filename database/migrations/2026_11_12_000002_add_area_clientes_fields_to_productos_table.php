<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Material de cada producto que ven los clientes en "Mis productos":
 * advertencia, videos (lista: titulo | foto | url) y documentos (lista: titulo | archivo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->text('advertencia')->nullable()->after('archivo');
            $table->json('videos')->nullable()->after('advertencia');
            $table->json('documentos')->nullable()->after('videos');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['advertencia', 'videos', 'documentos']);
        });
    }
};
