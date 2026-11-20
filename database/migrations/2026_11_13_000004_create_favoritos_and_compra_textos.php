<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Productos favoritos de cada comprador (corazón de las tarjetas)
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_usuario_id')->constrained('web_usuarios')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['web_usuario_id', 'producto_id']);
        });

        // Textos del carrito y la compra, editables en "Tienda > Info. de compra"
        Schema::table('infos', function (Blueprint $table) {
            $table->text('retiro_texto')->nullable();      // debajo de "Retiro en local"
            $table->text('pago_local_texto')->nullable();  // debajo de "Pago en local"
            $table->text('cuotas_texto')->nullable();      // debajo del precio en el detalle
            $table->string('privacidad_url')->nullable();  // link "Política de privacidad"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos');

        Schema::table('infos', function (Blueprint $table) {
            $table->dropColumn(['retiro_texto', 'pago_local_texto', 'cuotas_texto', 'privacidad_url']);
        });
    }
};
