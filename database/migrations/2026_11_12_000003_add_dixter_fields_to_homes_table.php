<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Textos del Home de Dixter 2.0 (el hero sale de home_banners, las familias de
 * familias, las industrias de sectores y el bloque "Nosotros" de nosotros).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homes', function (Blueprint $table) {
            // Tarjetas Soporte técnico / Post venta
            $table->string('soporte_etiqueta')->nullable();
            $table->string('soporte_titulo')->nullable();
            $table->text('soporte_texto')->nullable();
            $table->string('postventa_etiqueta')->nullable();
            $table->string('postventa_titulo')->nullable();
            $table->text('postventa_texto')->nullable();

            // Familias de productos
            $table->string('familias_etiqueta')->nullable();
            $table->string('familias_titulo')->nullable();

            // Bloque "Vendemos equipos hechos para durar"
            $table->json('beneficios')->nullable();   // [{destacado, texto}] x3
            $table->string('lema')->nullable();
            $table->string('lema_destacado')->nullable();

            // Industrias
            $table->string('sectores_etiqueta')->nullable();
            $table->string('sectores_titulo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('homes', function (Blueprint $table) {
            $table->dropColumn([
                'soporte_etiqueta', 'soporte_titulo', 'soporte_texto',
                'postventa_etiqueta', 'postventa_titulo', 'postventa_texto',
                'familias_etiqueta', 'familias_titulo',
                'beneficios', 'lema', 'lema_destacado',
                'sectores_etiqueta', 'sectores_titulo',
            ]);
        });
    }
};
