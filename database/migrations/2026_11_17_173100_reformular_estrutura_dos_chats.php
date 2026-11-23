<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * 1. Validar os dados antigos.
         */

        $totalChats = DB::table('chats')->count();
        $totalMensagens = DB::table('mensagems')->count();

        $totalRelacionados = DB::table('chats')
            ->join('negociacoes', 'chats.negociacao_id', '=', 'negociacoes.id')
            ->join('ofertas', 'negociacoes.oferta_id', '=', 'ofertas.id')
            ->whereNotNull('negociacoes.cliente_id')
            ->whereNotNull('ofertas.fornecedor_id')
            ->count();

        if ($totalChats !== $totalRelacionados) {
            throw new RuntimeException(
                'Migração interrompida: existem chats sem negociação, '
                . 'cliente ou fornecedor correspondente.'
            );
        }

        /*
         * 2. Preparar a nova estrutura.
         */

        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('cliente_id')
                ->nullable()
                ->after('id')
                ->constrained('users');

            $table->foreignId('fornecedor_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('fornecedores');
        });

        Schema::table('negociacoes', function (Blueprint $table) {
            $table->timestamp('entrega_confirmada_em')->nullable();
            $table->timestamp('encerrado_em')->nullable();
            $table->timestamp('excluir_mensagens_em')->nullable();
        });

        Schema::create('assuntos_chat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_id')
                ->constrained('chats')
                ->cascadeOnDelete();

            $table->string('titulo');

            $table->unsignedBigInteger('cliente_ultima_leitura_id')->nullable();
            $table->unsignedBigInteger('fornecedor_ultima_leitura_id')->nullable();

            $table->timestamps();
        });

        Schema::create('assunto_negociacao', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assunto_chat_id')
                ->constrained('assuntos_chat')
                ->cascadeOnDelete();

            $table->foreignId('negociacao_id')
                ->constrained('negociacoes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['assunto_chat_id', 'negociacao_id'],
                'assunto_negociacao_unico'
            );
        });

        Schema::table('mensagems', function (Blueprint $table) {
            $table->foreignId('assunto_chat_id')
                ->nullable()
                ->after('chat_id')
                ->constrained('assuntos_chat')
                ->nullOnDelete();
        });

        /*
         * 3. Buscar os chats antigos.
         */

        $chatsAntigos = DB::table('chats')
            ->join('negociacoes', 'chats.negociacao_id', '=', 'negociacoes.id')
            ->join('ofertas', 'negociacoes.oferta_id', '=', 'ofertas.id')
            ->select([
                'chats.id as chat_id',
                'chats.negociacao_id',
                'chats.ultima_mensagem_em',
                'chats.cliente_ultima_leitura_id',
                'chats.fornecedor_ultima_leitura_id',
                'chats.entrega_confirmada_em',
                'chats.encerrado_em',
                'chats.excluir_mensagens_em',
                'chats.created_at',
                'chats.updated_at',
                'negociacoes.cliente_id',
                'ofertas.fornecedor_id',
            ])
            ->orderBy('chats.id')
            ->get();

        /*
         * 4. Agrupar por cliente e fornecedor.
         */

        $grupos = [];

        foreach ($chatsAntigos as $chatAntigo) {
            $chave = $chatAntigo->cliente_id . ':' . $chatAntigo->fornecedor_id;
            $grupos[$chave][] = $chatAntigo;
        }

        /*
         * 5. Consolidar os chats.
         */

        DB::transaction(function () use ($grupos) {

            foreach ($grupos as $grupo) {

                $principal = $grupo[0];

                $datasUltimaMensagem = array_filter(
                    array_map(fn ($item) => $item->ultima_mensagem_em, $grupo)
                );

                $datasCriacao = array_filter(
                    array_map(fn ($item) => $item->created_at, $grupo)
                );

                $datasAtualizacao = array_filter(
                    array_map(fn ($item) => $item->updated_at, $grupo)
                );

                DB::table('chats')
                    ->where('id', $principal->chat_id)
                    ->update([
                        'cliente_id' => $principal->cliente_id,
                        'fornecedor_id' => $principal->fornecedor_id,
                        'ultima_mensagem_em' => $datasUltimaMensagem
                            ? max($datasUltimaMensagem)
                            : null,
                        'cliente_ultima_leitura_id' => null,
                        'fornecedor_ultima_leitura_id' => null,
                        'created_at' => $datasCriacao
                            ? min($datasCriacao)
                            : now(),
                        'updated_at' => $datasAtualizacao
                            ? max($datasAtualizacao)
                            : now(),
                    ]);

                foreach ($grupo as $chatAntigo) {

                    $assuntoId = DB::table('assuntos_chat')->insertGetId([
                        'chat_id' => $principal->chat_id,
                        'titulo' => 'Negociação #' . $chatAntigo->negociacao_id,
                        'cliente_ultima_leitura_id' => $chatAntigo->cliente_ultima_leitura_id,
                        'fornecedor_ultima_leitura_id' => $chatAntigo->fornecedor_ultima_leitura_id,
                        'created_at' => $chatAntigo->created_at ?? now(),
                        'updated_at' => $chatAntigo->updated_at ?? now(),
                    ]);

                    DB::table('assunto_negociacao')->insert([
                        'assunto_chat_id' => $assuntoId,
                        'negociacao_id' => $chatAntigo->negociacao_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('mensagems')
                        ->where('chat_id', $chatAntigo->chat_id)
                        ->update([
                            'chat_id' => $principal->chat_id,
                            'assunto_chat_id' => $assuntoId,
                        ]);

                    DB::table('negociacoes')
                        ->where('id', $chatAntigo->negociacao_id)
                        ->update([
                            'entrega_confirmada_em' => $chatAntigo->entrega_confirmada_em,
                            'encerrado_em' => $chatAntigo->encerrado_em,
                            'excluir_mensagens_em' => $chatAntigo->excluir_mensagens_em,
                        ]);
                }

                $idsDuplicados = array_map(
                    fn ($item) => $item->chat_id,
                    array_slice($grupo, 1)
                );

                if (!empty($idsDuplicados)) {
                    DB::table('chats')->whereIn('id', $idsDuplicados)->delete();
                }
            }
        });

        /*
         * 6. Validar a consolidação.
         */

        $totalMensagensDepois = DB::table('mensagems')->count();

        $totalAssuntos = DB::table('assuntos_chat')->count();

        $chatsSemParticipantes = DB::table('chats')
            ->whereNull('cliente_id')
            ->orWhereNull('fornecedor_id')
            ->count();

        $mensagensSemAssunto = DB::table('mensagems')
            ->whereNull('assunto_chat_id')
            ->count();

        $assuntosSemNegociacao = DB::table('assuntos_chat')
            ->leftJoin(
                'assunto_negociacao',
                'assuntos_chat.id',
                '=',
                'assunto_negociacao.assunto_chat_id'
            )
            ->whereNull('assunto_negociacao.id')
            ->count();

        $negociacoesSemAssunto = DB::table('negociacoes')
            ->whereIn(
                'id',
                $chatsAntigos->pluck('negociacao_id')->unique()
            )
            ->whereNotIn(
                'id',
                DB::table('assunto_negociacao')->select('negociacao_id')
            )
            ->count();

        if (
            $totalMensagens !== $totalMensagensDepois ||
            $totalAssuntos !== $totalChats ||
            $chatsSemParticipantes > 0 ||
            $mensagensSemAssunto > 0 ||
            $assuntosSemNegociacao > 0 ||
            $negociacoesSemAssunto > 0
        ) {
            throw new RuntimeException(
                'Migração interrompida: a validação dos dados falhou. '
                . 'A estrutura antiga não será removida.'
            );
        }

        /*
         * 7. Tornar os participantes obrigatórios.
         */

        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('cliente_id')->nullable(false)->change();
            $table->unsignedBigInteger('fornecedor_id')->nullable(false)->change();
        });

        /*
         * 8. Remover o vínculo antigo.
         */

        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign('chats_negociacao_id_foreign');
            $table->dropUnique('chats_negociacao_id_unique');

            $table->dropColumn([
                'negociacao_id',
                'entrega_confirmada_em',
                'encerrado_em',
                'excluir_mensagens_em',
            ]);
        });

        /*
         * 9. Garantir uma conversa por cliente e fornecedor.
         */

        Schema::table('chats', function (Blueprint $table) {
            $table->unique(
                ['cliente_id', 'fornecedor_id'],
                'chat_cliente_fornecedor_unico'
            );
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Esta migration não possui reversão automática segura.'
        );
    }
};
