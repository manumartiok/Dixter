<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clientes del "Área clientes" (login con modal, sin registro).
 * Se crean solo desde el administrador y ven los productos que se les asignan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('usuario')->unique();
            $table->string('email')->nullable();
            $table->string('contraseña');
            $table->string('empresa')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('cliente_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cliente_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_producto');
        Schema::dropIfExists('clientes');
    }
};
