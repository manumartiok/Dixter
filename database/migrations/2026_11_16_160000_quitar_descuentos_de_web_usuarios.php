<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Compradores: Dixter no maneja descuentos por usuario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_usuarios', function (Blueprint $table) {
            $table->dropColumn(['descuento1', 'descuento2', 'descuento3', 'descuento4', 'descuento5']);
        });
    }

    public function down(): void
    {
        Schema::table('web_usuarios', function (Blueprint $table) {
            foreach ([1, 2, 3, 4, 5] as $n) {
                $table->string("descuento{$n}")->nullable();
            }
        });
    }
};
