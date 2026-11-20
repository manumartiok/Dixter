<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Checkout de Dixter 2.0: datos de facturación, envío (en "entrega"), forma de pago
 * y estado del pago con Mercado Pago (mismas columnas que la implementación de BBS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('nombre')->nullable();
            $table->string('dni')->nullable();
            $table->string('cuit')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->json('entrega_otra')->nullable();

            $table->string('pago_estado')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('mp_payment_id')->nullable();
            $table->string('mp_status_detail')->nullable();
            $table->timestamp('pagado_at')->nullable();
        });

        Schema::table('pedido_items', function (Blueprint $table) {
            $table->decimal('iva', 4, 1)->default(0)->after('precio_con_descuento');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'nombre', 'dni', 'cuit', 'razon_social', 'email', 'celular', 'direccion', 'localidad',
                'provincia', 'codigo_postal', 'entrega_otra',
                'pago_estado', 'mp_preference_id', 'mp_payment_id', 'mp_status_detail', 'pagado_at',
            ]);
        });

        Schema::table('pedido_items', function (Blueprint $table) {
            $table->dropColumn('iva');
        });
    }
};
