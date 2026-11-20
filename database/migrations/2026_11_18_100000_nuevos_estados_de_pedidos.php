<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Estados de pedido: En espera, Procesando, Despachado, Falló, Completado y Cancelado.
 * Los pedidos existentes pasan a su equivalente.
 */
return new class extends Migration
{
    private array $equivalencias = [
        'en_preparacion' => 'en_espera',
        'listo' => 'procesando',
        'entregado' => 'completado',
    ];

    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado')->default('en_espera')->change();
        });

        foreach ($this->equivalencias as $viejo => $nuevo) {
            DB::table('pedidos')->where('estado', $viejo)->update(['estado' => $nuevo]);
        }

        // Pagos de Mercado Pago ya resueltos
        DB::table('pedidos')->where('estado', 'en_espera')->where('pago_estado', 'aprobado')->update(['estado' => 'procesando']);
        DB::table('pedidos')->where('estado', 'en_espera')->whereIn('pago_estado', ['rechazado', 'cancelado'])->update(['estado' => 'fallido']);
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado')->default('en_preparacion')->change();
        });

        foreach (['en_espera' => 'en_preparacion', 'fallido' => 'en_preparacion', 'procesando' => 'listo', 'despachado' => 'listo', 'completado' => 'entregado'] as $nuevo => $viejo) {
            DB::table('pedidos')->where('estado', $nuevo)->update(['estado' => $viejo]);
        }
    }
};
