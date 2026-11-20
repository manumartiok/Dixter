<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La "Política de privacidad" deja de ser un link externo: el texto completo se escribe en
 * Tienda > Info. de compra y se muestra en /politica-de-privacidad. longText admite textos muy largos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infos', function (Blueprint $table) {
            $table->longText('privacidad')->nullable();
            $table->dropColumn('privacidad_url');
        });
    }

    public function down(): void
    {
        Schema::table('infos', function (Blueprint $table) {
            $table->string('privacidad_url')->nullable();
            $table->dropColumn('privacidad');
        });
    }
};
