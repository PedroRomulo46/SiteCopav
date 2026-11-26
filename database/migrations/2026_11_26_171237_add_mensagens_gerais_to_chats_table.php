
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->string('mensagens_gerais_status')
                ->default('inativo');

            $table->foreignId('mensagens_gerais_solicitado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('mensagens_gerais_solicitada_em')
                ->nullable();

            $table->foreignId('mensagens_gerais_aprovado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('mensagens_gerais_ativada_em')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign([
                'mensagens_gerais_solicitado_por'
            ]);

            $table->dropForeign([
                'mensagens_gerais_aprovado_por'
            ]);

            $table->dropColumn([
                'mensagens_gerais_status',
                'mensagens_gerais_solicitado_por',
                'mensagens_gerais_solicitada_em',
                'mensagens_gerais_aprovado_por',
                'mensagens_gerais_ativada_em',
            ]);
        });
    }
};
