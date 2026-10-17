<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('negociacao_id')
                ->unique()
                ->constrained('negociacoes')
                ->cascadeOnDelete();

            $table->timestamp('entrega_confirmada_em')->nullable();

            $table->timestamp('ultima_mensagem_em')->nullable();

            $table->timestamp('encerrado_em')->nullable();

            $table->timestamp('excluir_mensagens_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};