<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contacto: el mapa sale de latitud/longitud (Leaflet), así que el iframe ya no se usa.
 * El diseño muestra un solo mail y un solo teléfono.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropColumn(['ubicacion', 'mail2', 'mail3', 'telefono2']);
        });
    }

    public function down(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->text('ubicacion')->nullable();
            $table->string('mail2')->nullable();
            $table->string('mail3')->nullable();
            $table->string('telefono2')->nullable();
        });
    }
};
