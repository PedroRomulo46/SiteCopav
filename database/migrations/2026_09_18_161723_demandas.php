<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->onDelete('cascade');

            $table->string('nome_produto');
            $table->text('descricao')->nullable();

            $table->decimal('quantidade', 12, 2);
            $table->string('unidade', 50);

            $table->decimal('valor_maximo', 12, 2)->nullable();

            $table->string('localizacao')->nullable();

            $table->date('data_limite')->nullable();

            $table->enum('status', [
                'aberta',
                'encerrada',
                'cancelada'
            ])->default('aberta');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandas');
    }
};