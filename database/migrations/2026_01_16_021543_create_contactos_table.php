<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);
            $table->string('correo', 150);
            $table->string('telefono', 20)->nullable();

            // productos | pedidos | pagos | sugerencias
            $table->string('tipo', 20);

            $table->text('mensaje');

            // Para uso futuro (no obligatorio ahora)
            $table->string('estado', 20)->default('NUEVO');

            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
