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
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('negociacao_id')
                ->constrained('negociacoes')
                ->onDelete('cascade');

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->decimal('valor', 12, 2);
            $table->decimal('quantidade', 12, 2)->nullable();

            $table->text('observacao')->nullable();

            $table->enum('status', [
                'pendente',
                'aceita',
                'recusada'
            ])->default('pendente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propostas');
    }
};
