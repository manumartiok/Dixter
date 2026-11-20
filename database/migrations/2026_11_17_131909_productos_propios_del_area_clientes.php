<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El Área clientes tiene sus propios productos (producto_clientes), distintos de los de la tienda.
 * - La asignación a clientes pasa de cliente_producto a cliente_producto_cliente.
 * - Los productos de la tienda dejan de tener advertencia, videos y documentos del Área clientes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cliente_producto');

        Schema::create('cliente_producto_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('producto_cliente_id')->constrained('producto_clientes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cliente_id', 'producto_cliente_id']);
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['advertencia', 'videos', 'documentos']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->text('advertencia')->nullable();
            $table->json('videos')->nullable();
            $table->json('documentos')->nullable();
        });

        Schema::dropIfExists('cliente_producto_cliente');

        Schema::create('cliente_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cliente_id', 'producto_id']);
        });
    }
};
