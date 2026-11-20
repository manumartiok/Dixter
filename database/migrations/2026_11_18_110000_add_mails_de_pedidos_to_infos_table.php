<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Casilla de ventas que recibe los avisos de pedidos, y otra opcional en copia (CC) */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infos', function (Blueprint $table) {
            $table->string('mail_ventas')->nullable();
            $table->string('mail_ventas_cc')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('infos', function (Blueprint $table) {
            $table->dropColumn(['mail_ventas', 'mail_ventas_cc']);
        });
    }
};
