<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fornecedor_id')->constrained('fornecedores')->onDelete('cascade');
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');

            $table->decimal('quantidade', 12, 2);
            $table->decimal('valor', 12, 2);
            $table->string('unidade');

            $table->string('localizacao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_validade')->nullable();

            $table->enum('status', [
                'rascunho',
                'publicada',
                'encerrada',
                'cancelada'
            ])->default('rascunho');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
