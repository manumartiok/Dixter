<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Productos del Área clientes: texto de "Advertencia." arriba de los videos (Figma: Área clientes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto_clientes', function (Blueprint $table) {
            $table->text('advertencia')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('producto_clientes', function (Blueprint $table) {
            $table->dropColumn('advertencia');
        });
    }
};
