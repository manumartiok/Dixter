<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Área clientes: todos los clientes ven todos los productos activos, ya no se asignan uno por uno.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cliente_producto_cliente');
    }

    public function down(): void
    {
        Schema::create('cliente_producto_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('producto_cliente_id')->constrained('producto_clientes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cliente_id', 'producto_cliente_id']);
        });
    }
};
