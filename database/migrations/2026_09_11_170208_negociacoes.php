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
        Schema::create('negociacoes', function (Blueprint $table) {
           $table->id();

            $table->foreignId('oferta_id')
                  ->constrained('ofertas')
                  ->onDelete('cascade');

            $table->foreignId('cliente_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->enum('status', [
                'pendente',
                'em_negociacao',
                'aceita',
                'recusada',
                'concluida',
                'cancelada'
            ])->default('pendente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negociacoes');
    }
};
