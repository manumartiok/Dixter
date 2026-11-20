<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos del catálogo de Dixter 2.0:
 * - categoria_id: categoría más específica del árbol.
 * - precio_anterior: si es mayor al precio, el producto se muestra en OFERTA.
 * - iva: 0, 10.5 o 21. El precio cargado lo incluye; si es > 0 se muestra el valor "sin IVA".
 * - descripcion (texto con formato), informacion y ficha (listas nombre | valor),
 *   instructivos (titulo | archivo) y videos_web (titulo | foto | url): material público del detalle.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->after('marca_id')->constrained('categorias')->nullOnDelete();
            $table->decimal('precio_anterior', 12, 2)->nullable()->after('precio');
            $table->decimal('iva', 4, 1)->default(21)->after('precio_anterior');
            $table->text('descripcion')->nullable()->after('nombre');
            $table->json('informacion')->nullable()->after('descripcion');
            $table->json('ficha')->nullable()->after('informacion');
            $table->json('instructivos')->nullable()->after('ficha');
            $table->json('videos_web')->nullable()->after('instructivos');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
            $table->dropColumn(['precio_anterior', 'iva', 'descripcion', 'informacion', 'ficha', 'instructivos', 'videos_web']);
        });
    }
};
