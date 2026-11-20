<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nosotros: agrega el Logo 4 ("¿Por qué elegirnos?" usa los logos 1 a 4) y quita el video.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            $table->string('icono4')->nullable()->after('texto_icono3');
            $table->string('nombre_icono4')->nullable()->after('icono4');
            $table->text('texto_icono4')->nullable()->after('nombre_icono4');
            $table->dropColumn('video');
        });

        // Los "Pilares" pasaron a ser los logos de Nosotros
        Schema::dropIfExists('pilares');
    }

    public function down(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            $table->dropColumn(['icono4', 'nombre_icono4', 'texto_icono4']);
            $table->string('video')->nullable();
        });
    }
};
