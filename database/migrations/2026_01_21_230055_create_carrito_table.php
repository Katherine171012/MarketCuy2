<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('carrito', function (Blueprint $table) {
            $table->id();

            // 1. id_user debe ser entero para coincidir con el 'serial' de la tabla users
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            // 2. id_producto DEBE ser char(7) para coincidir con tu tabla productos
            $table->char('id_producto', 7);
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');

            $table->integer('cantidad');

            $table->timestamps();

            // Evitar que el mismo usuario tenga el mismo producto duplicado en la tabla
            $table->unique(['id_user', 'id_producto']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('carrito');
    }
};
