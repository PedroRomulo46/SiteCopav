<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_ultima_leitura_id')
                ->nullable()
                ->after('ultima_mensagem_em');

            $table->unsignedBigInteger('fornecedor_ultima_leitura_id')
                ->nullable()
                ->after('cliente_ultima_leitura_id');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn([
                'cliente_ultima_leitura_id',
                'fornecedor_ultima_leitura_id',
            ]);
        });
    }
};