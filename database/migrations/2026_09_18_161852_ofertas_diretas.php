<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas_diretas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('demanda_id')
                ->constrained('demandas')
                ->onDelete('cascade');

            $table->foreignId('fornecedor_id')
                ->constrained('fornecedores')
                ->onDelete('cascade');

            $table->decimal('quantidade', 12, 2);

            $table->decimal('valor', 12, 2);

            $table->text('observacao')->nullable();

            $table->enum('status', [
                'pendente',
                'aceita',
                'recusada'
            ])->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas_diretas');
    }
};