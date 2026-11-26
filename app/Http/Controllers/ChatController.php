<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensagem;
use App\Models\Negociacao;
use App\Models\AssuntoChat;
use App\Events\MensagemEnviada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Events\MensagensGeraisAtualizadas;

class ChatController extends Controller
{
    /**
     * Verifica se o usuário participa da conversa.
     */
    private function verificarParticipante(Chat $chat, $usuario): bool
    {
        if ($usuario->user_type === 'admin') {
            return true;
        }

        if ((int) $chat->cliente_id === (int) $usuario->id) {
            return true;
        }

        return $usuario->fornecedor
            && (int) $chat->fornecedor_id === (int) $usuario->fornecedor->id;
    }

    /**
     * Identifica se o usuário é o cliente da conversa.
     */
    private function ehCliente(Chat $chat, $usuario): bool
    {
        return (int) $chat->cliente_id === (int) $usuario->id;
    }

    /**
     * Verifica se o usuário é um dos participantes reais do chat.
     * Administradores podem consultar o chat, mas não solicitar
     * nem aprovar a liberação de mensagens gerais.
     */
    private function ehParticipanteReal(Chat $chat, $usuario): bool
    {
        if ((int) $chat->cliente_id === (int) $usuario->id) {
            return true;
        }

        return $usuario->fornecedor
            && (int) $chat->fornecedor_id === (int) $usuario->fornecedor->id;
    }

    /**
     * Solicita a liberação das mensagens gerais.
     */
    public function solicitarMensagensGerais(Chat $chat)
    {
        $usuario = auth()->user();

        abort_unless($this->ehParticipanteReal($chat, $usuario), 403);

        $resultado = DB::transaction(function () use ($chat, $usuario) {
            $chat = Chat::whereKey($chat->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($chat->mensagens_gerais_status !== 'inativo') {
                return false;
            }

            $chat->update([
                'mensagens_gerais_status' => 'solicitado',
                'mensagens_gerais_solicitado_por' => $usuario->id,
                'mensagens_gerais_solicitada_em' => now(),
                'mensagens_gerais_aprovado_por' => null,
                'mensagens_gerais_ativada_em' => null,
            ]);

            return true;
        });

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma solicitação ou o chat geral já está liberado.',
                'status' => $chat->fresh()->mensagens_gerais_status,
            ], 409);
        }

        $chatAtualizado = $chat->fresh();

        broadcast(
            new MensagensGeraisAtualizadas($chatAtualizado)
        )->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Solicitação enviada. Aguardando aprovação do outro participante.',
            'status' => 'solicitado',
            'solicitado_por' => $usuario->id,
        ]);
    }

    /**
     * Aprova a solicitação de liberação das mensagens gerais.
     */
    public function aprovarMensagensGerais(Chat $chat)
    {
        $usuario = auth()->user();

        abort_unless($this->ehParticipanteReal($chat, $usuario), 403);

        $resultado = DB::transaction(function () use ($chat, $usuario) {
            $chat = Chat::whereKey($chat->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($chat->mensagens_gerais_status !== 'solicitado') {
                return false;
            }

            // Quem solicitou não pode aprovar a própria solicitação.
            if ((int) $chat->mensagens_gerais_solicitado_por === (int) $usuario->id) {
                abort(403, 'Você não pode aprovar sua própria solicitação.');
            }

            $chat->update([
                'mensagens_gerais_status' => 'ativo',
                'mensagens_gerais_aprovado_por' => $usuario->id,
                'mensagens_gerais_ativada_em' => now(),
            ]);

            return true;
        });

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'message' => 'Não existe uma solicitação pendente para aprovar.',
                'status' => $chat->fresh()->mensagens_gerais_status,
            ], 409);
        }

        $chatAtualizado = $chat->fresh();

        broadcast(
            new MensagensGeraisAtualizadas($chatAtualizado)
        )->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Chat geral liberado com sucesso.',
            'status' => 'ativo',
            'aprovado_por' => $usuario->id,
        ]);
    }

    /**
     * Página principal do chat.
     */
    public function index()
    {
        $usuario = auth()->user();

        $query = Chat::query()
            ->with([
                'cliente',
                'fornecedor.usuario',
                'assuntos.negociacoes.oferta.produto',
                'mensagens' => function ($query) {
                    $query->latest()->limit(1);
                },
            ]);

        if ($usuario->user_type !== 'admin') {
            $query->where(function ($q) use ($usuario) {
                $q->where('cliente_id', $usuario->id);

                if ($usuario->fornecedor) {
                    $q->orWhere('fornecedor_id', $usuario->fornecedor->id);
                }
            });
        }

        $chats = $query
            ->orderByDesc('ultima_mensagem_em')
            ->get();

        return view('chat.index', compact('chats'));
    }
    
    /**
     * Lista as conversas e seus contadores de mensagens não lidas.
     * Mantém os contadores separados entre mensagens gerais e assuntos.
     */
    public function conversas()
    {
        $usuario = auth()->user();

        $query = Chat::with([
            'cliente',
            'fornecedor.usuario',
            'mensagens' => fn ($q) => $q->latest()->limit(1),
            'assuntos.negociacoes.oferta.produto',
        ]);

        if ($usuario->user_type !== 'admin') {
            $query->where(function ($q) use ($usuario) {
                $q->where('cliente_id', $usuario->id);

                if ($usuario->fornecedor) {
                    $q->orWhere('fornecedor_id', $usuario->fornecedor->id);
                }
            });
        }

        $chats = $query
            ->orderByDesc('ultima_mensagem_em')
            ->get();

        $conversas = $chats->map(function ($chat) use ($usuario) {

            $ehCliente = $this->ehCliente($chat, $usuario);

            $participante = $ehCliente
                ? (
                    $chat->fornecedor?->usuario?->nome
                    ?? $chat->fornecedor?->nome
                )
                : $chat->cliente?->nome;

            $campoLeitura = $ehCliente
                ? 'cliente_ultima_leitura_id'
                : 'fornecedor_ultima_leitura_id';

            /*
            * MENSAGENS GERAIS
            */
            $statusMensagensGerais = $chat->mensagens_gerais_status ?? 'inativo';

            $mensagensGeraisAtivas = $statusMensagensGerais === 'ativo';

            $consultaGerais = $chat->mensagens()
                ->whereNull('assunto_chat_id')
                ->where('usuario_id', '!=', $usuario->id);

            $ultimaLeituraGeral = $chat->$campoLeitura;

            if ($ultimaLeituraGeral !== null) {
                $consultaGerais->where('id', '>', $ultimaLeituraGeral);
            }

            // Enquanto não houver aprovação, as mensagens gerais
            // não aparecem nos contadores nem no histórico.
            $naoLidasGerais = $mensagensGeraisAtivas
                ? (clone $consultaGerais)->count()
                : 0;

            $ultimaMensagemGeral = $mensagensGeraisAtivas
                ? $chat->mensagens()
                    ->whereNull('assunto_chat_id')
                    ->latest('id')
                    ->first()
                : null;

            $souSolicitanteMensagensGerais =
                (int) $chat->mensagens_gerais_solicitado_por === (int) $usuario->id;

            $podeAprovarMensagensGerais =
                $statusMensagensGerais === 'solicitado'
                && !$souSolicitanteMensagensGerais
                && $this->ehParticipanteReal($chat, $usuario);

            /*
            * ASSUNTOS
            */
            $assuntos = $chat->assuntos->map(function ($assunto) use (
                $chat,
                $usuario,
                $campoLeitura
            ) {

                $consulta = Mensagem::where('chat_id', $chat->id)
                    ->where('assunto_chat_id', $assunto->id)
                    ->where('usuario_id', '!=', $usuario->id);

                $ultimaLeitura = $assunto->$campoLeitura;

                if ($ultimaLeitura !== null) {
                    $consulta->where('id', '>', $ultimaLeitura);
                }

                $naoLidas = (clone $consulta)->count();

                $ultimaMensagem = $chat->mensagens()
                    ->where('assunto_chat_id', $assunto->id)
                    ->latest('id')
                    ->first();

                /*
                * Produtos associados ao assunto.
                * Um mesmo produto aparece apenas uma vez.
                */
                $produtos = $assunto->negociacoes
                    ->map(function ($negociacao) {
                        $produto = $negociacao->oferta?->produto;

                        if (!$produto) {
                            return null;
                        }

                        return [
                            'id' => $produto->id,
                            'nome' => $produto->nome,
                            'imagem' => $produto->imagem
                                ? (
                                    str_starts_with($produto->imagem, 'assets/')
                                        ? asset($produto->imagem)
                                        : Storage::url($produto->imagem)
                                )
                                : null,
                        ];
                    })
                    ->filter()
                    ->unique('id')
                    ->values();

                return [
                    'id' => $assunto->id,
                    'titulo' => $assunto->titulo,

                    'mensagens_nao_lidas' => $naoLidas,

                    'ultima_mensagem' => $ultimaMensagem?->conteudo
                        ?? 'Nenhuma mensagem ainda',

                    'ultima_mensagem_usuario' => $ultimaMensagem?->usuario_id,

                    'ultima_mensagem_data' => $ultimaMensagem?->created_at
                        ? $ultimaMensagem->created_at->utc()->toISOString()
                        : null,

                    'negociacoes' => $assunto->negociacoes->pluck('id'),

                    'produtos' => $produtos,

                    'links_negociacoes' => $assunto->negociacoes
                        ->map(function ($negociacao) use ($assunto, $chat) {
                            $produto = $negociacao->oferta?->produto;

                            return [
                                'id' => $negociacao->id,
                                'titulo' => 'Negociação #' . $negociacao->id,
                                'produto' => $produto?->nome ?? 'Negociação',
                                'url' => route('negociacoes.show', $negociacao),
                            ];
                        })
                        ->values(),
                ];
            })->values();

            /*
            * TOTAL DE NÃO LIDAS:
            * mensagens gerais + mensagens de todos os assuntos.
            */
            $naoLidasAssuntos = $assuntos->sum('mensagens_nao_lidas');

            $totalNaoLidas = $naoLidasGerais + $naoLidasAssuntos;

            /*
            * ÚLTIMA MENSAGEM VISÍVEL NA LISTA DE CONVERSAS
            * Enquanto o chat geral não estiver ativo, mensagens gerais
            * não podem aparecer nem mesmo na prévia da conversa.
            */
            $ultimaMensagemQuery = $chat->mensagens();

            if (!$mensagensGeraisAtivas) {
                $ultimaMensagemQuery->whereNotNull('assunto_chat_id');
            }

            $ultimaMensagem = $ultimaMensagemQuery
                ->latest('id')
                ->first();

            return [
                'chat_id' => $chat->id,

                'participante' => $participante ?? 'Participante',

                'mensagens_gerais' => [
                    'assunto_chat_id' => null,

                    'status' => $statusMensagensGerais,

                    'solicitado_por' => $chat->mensagens_gerais_solicitado_por,

                    'sou_solicitante' => $souSolicitanteMensagensGerais,

                    'pode_aprovar' => $podeAprovarMensagensGerais,

                    'solicitada_em' => $chat->mensagens_gerais_solicitada_em
                        ? $chat->mensagens_gerais_solicitada_em->utc()->toISOString()
                        : null,

                    'ativada_em' => $chat->mensagens_gerais_ativada_em
                        ? $chat->mensagens_gerais_ativada_em->utc()->toISOString()
                        : null,

                    'mensagens_nao_lidas' => $naoLidasGerais,

                    'ultima_mensagem' => $ultimaMensagemGeral?->conteudo
                        ?? 'Nenhuma mensagem ainda',

                    'ultima_mensagem_usuario' => $ultimaMensagemGeral?->usuario_id,

                    'ultima_mensagem_data' => $ultimaMensagemGeral?->created_at
                        ? $ultimaMensagemGeral->created_at->utc()->toISOString()
                        : null,
                ],

                'ultima_mensagem' => $ultimaMensagem?->conteudo
                    ?? 'Nenhuma mensagem ainda',

                'ultima_mensagem_usuario' => $ultimaMensagem?->usuario_id,

                'ultima_mensagem_data' => $ultimaMensagem?->created_at
                    ? $ultimaMensagem->created_at->utc()->toISOString()
                    : null,

                'ultima_mensagem_assunto_chat_id' => $ultimaMensagem?->assunto_chat_id,

                'mensagens_gerais_nao_lidas' => $naoLidasGerais,

                'mensagens_nao_lidas' => $totalNaoLidas,

                'assuntos' => $assuntos,

                'url' => route('chat.show', $chat->id),
            ];
        });

        return response()->json($conversas);
    }

    /**
     * Lista os assuntos de uma conversa.
     */
    public function assuntos(Chat $chat)
    {
        $usuario = auth()->user();

        abort_unless($this->verificarParticipante($chat, $usuario), 403);

        $chat->load('assuntos.negociacoes');

        return response()->json(
            $chat->assuntos->map(fn ($assunto) => [
                'id' => $assunto->id,
                'titulo' => $assunto->titulo,
                'negociacoes' => $assunto->negociacoes->pluck('id'),
            ])
        );
    }

    /**
     * Abre uma conversa.
     */
    public function show(Chat $chat)
    {
        $usuario = auth()->user();

        abort_unless($this->verificarParticipante($chat, $usuario), 403);

        $chat->load([
            'cliente',
            'fornecedor.usuario',
            'assuntos.negociacoes.oferta.produto',
        ]);

        $assuntos = $chat->assuntos;

        return view('chat.show', compact('chat', 'assuntos'));
    }

    /**
     * Marca como lidas as mensagens gerais ou de um assunto.
     */
    private function marcarComoLida(Chat $chat, $usuario, ?int $assuntoId = null): void
    {
        $mensagens = $chat->mensagens();

        if ($assuntoId !== null) {
            $assunto = $chat->assuntos()->findOrFail($assuntoId);

            $ultimaMensagemId = $mensagens
                ->where('assunto_chat_id', $assunto->id)
                ->max('id');

            if (!$ultimaMensagemId) {
                return;
            }

            $campo = $this->ehCliente($chat, $usuario)
                ? 'cliente_ultima_leitura_id'
                : 'fornecedor_ultima_leitura_id';

            $assunto->update([
                $campo => $ultimaMensagemId,
            ]);

            return;
        }

        $ultimaMensagemId = $mensagens
            ->whereNull('assunto_chat_id')
            ->max('id');

        if (!$ultimaMensagemId) {
            return;
        }

        $campo = $this->ehCliente($chat, $usuario)
            ? 'cliente_ultima_leitura_id'
            : 'fornecedor_ultima_leitura_id';

        $chat->update([
            $campo => $ultimaMensagemId,
        ]);
    }

    /**
     * Busca as mensagens de uma conversa ou assunto.
     */
    public function mensagens(Request $request, Chat $chat)
    {
        $usuario = auth()->user();

        abort_unless($this->verificarParticipante($chat, $usuario), 403);

        $dados = $request->validate([
            'assunto_chat_id' => ['nullable', 'integer'],
        ]);

        $assuntoId = $dados['assunto_chat_id'] ?? null;

        $assunto = null;

        if ($assuntoId !== null) {
            $assunto = $chat->assuntos()->findOrFail($assuntoId);
        }

        /*
        * Mensagens gerais só podem ser acessadas depois da aprovação.
        * Os assuntos das negociações continuam independentes.
        */
        if (
            $assuntoId === null
            && ($chat->mensagens_gerais_status ?? 'inativo') !== 'ativo'
        ) {
            $status = $chat->mensagens_gerais_status ?? 'inativo';

            return response()->json([
                'chat_id' => $chat->id,
                'assunto_chat_id' => null,
                'status' => $status,
                'solicitado_por' => $chat->mensagens_gerais_solicitado_por,
                'sou_solicitante' =>
                    (int) $chat->mensagens_gerais_solicitado_por === (int) $usuario->id,
                'pode_aprovar' =>
                    $status === 'solicitado'
                    && (int) $chat->mensagens_gerais_solicitado_por !== (int) $usuario->id
                    && $this->ehParticipanteReal($chat, $usuario),
                'mensagens_nao_lidas' => 0,
                'primeira_mensagem_nao_lida_id' => null,
                'mensagens' => [],
            ]);
        }

        /*
        * IDENTIFICA O CAMPO DE LEITURA DO USUÁRIO
        */
        $campoLeitura = $this->ehCliente($chat, $usuario)
            ? 'cliente_ultima_leitura_id'
            : 'fornecedor_ultima_leitura_id';

        /*
        * RECUPERA A ÚLTIMA LEITURA ANTES DE ATUALIZÁ-LA
        */
        $ultimaLeitura = $assunto
            ? $assunto->$campoLeitura
            : $chat->$campoLeitura;

        /*
        * IDENTIFICA AS MENSAGENS NÃO LIDAS DESTE CONTEXTO
        */
        $consultaNaoLidas = $chat->mensagens()
            ->where('usuario_id', '!=', $usuario->id);

        if ($assuntoId !== null) {
            $consultaNaoLidas->where('assunto_chat_id', $assuntoId);
        } else {
            $consultaNaoLidas->whereNull('assunto_chat_id');
        }

        if ($ultimaLeitura !== null) {
            $consultaNaoLidas->where('id', '>', $ultimaLeitura);
        }

        $mensagensNaoLidas = (clone $consultaNaoLidas)->count();

        $primeiraMensagemNaoLidaId = $mensagensNaoLidas > 0
            ? (clone $consultaNaoLidas)->orderBy('id')->value('id')
            : null;

        /*
        * AGORA SIM: ATUALIZA O ÚLTIMO ID LIDO
        */
        $this->marcarComoLida($chat, $usuario, $assuntoId);

        /*
        * RECUPERA O HISTÓRICO
        */
        $query = $chat->mensagens()
            ->with('usuario:id,nome');

        if ($assuntoId !== null) {
            $query->where('assunto_chat_id', $assuntoId);
        } else {
            $query->whereNull('assunto_chat_id');
        }

        $mensagens = $query
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($mensagem) => [
                'id' => $mensagem->id,
                'chat_id' => $mensagem->chat_id,
                'assunto_chat_id' => $mensagem->assunto_chat_id,
                'usuario_id' => $mensagem->usuario_id,
                'nome' => $mensagem->usuario->nome ?? 'Participante',
                'conteudo' => $mensagem->conteudo,
                'created_at' => $mensagem->created_at->utc()->toISOString(),
            ]);

        return response()->json([
            'chat_id' => $chat->id,
            'assunto_chat_id' => $assuntoId,

            // Informações necessárias para o divisor.
            'mensagens_nao_lidas' => $mensagensNaoLidas,
            'primeira_mensagem_nao_lida_id' => $primeiraMensagemNaoLidaId,

            'mensagens' => $mensagens,
        ]);
    }

    /**
     * Envia uma mensagem geral ou vinculada a um assunto.
     */
    public function store(Request $request, Chat $chat)
    {
        $usuario = $request->user();

        abort_unless($this->verificarParticipante($chat, $usuario), 403);

        $dados = $request->validate([
            'conteudo' => ['required', 'string', 'max:5000'],
            'assunto_chat_id' => ['nullable', 'integer'],
        ]);

        $assuntoId = $dados['assunto_chat_id'] ?? null;

        if ($assuntoId !== null) {
            $chat->assuntos()->findOrFail($assuntoId);
        }

        // Bloqueia mensagens gerais enquanto não houver aprovação.
        if ($assuntoId === null) {
            abort_unless(
                $chat->mensagens_gerais_status === 'ativo',
                403,
                'As mensagens gerais ainda não foram liberadas.'
            );
        }

        $mensagem = DB::transaction(function () use (
            $chat,
            $usuario,
            $dados,
            $assuntoId
        ) {
            $mensagem = Mensagem::create([
                'chat_id' => $chat->id,
                'assunto_chat_id' => $assuntoId,
                'usuario_id' => $usuario->id,
                'conteudo' => trim($dados['conteudo']),
            ]);

            $chat->update([
                'ultima_mensagem_em' => $mensagem->created_at,
            ]);

            return $mensagem;
        });

        $mensagem->load('usuario');

        /*
        * Ao enviar uma mensagem, o contexto é considerado lido.
        * Isso também atualiza o último ID lido no banco de dados.
        */
        $this->marcarComoLida($chat, $usuario, $assuntoId);

        broadcast(new MensagemEnviada($mensagem))->toOthers();

        return response()->json([
            'success' => true,
            'mensagem' => [
                'id' => $mensagem->id,
                'chat_id' => $mensagem->chat_id,
                'assunto_chat_id' => $mensagem->assunto_chat_id,
                'usuario_id' => $mensagem->usuario_id,
                'nome' => $usuario->nome,
                'conteudo' => $mensagem->conteudo,
                'created_at' => $mensagem->created_at->utc()->toISOString(),
            ],
        ], 201);
    }

    /**
     * Abre ou cria uma conversa a partir de uma negociação aceita.
     * Mantém um ponto de entrada para negociações antigas e novas.
     */
    public function abrirNegociacao(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->load('oferta.fornecedor');

        $ehCliente = (int) $negociacao->cliente_id === (int) $usuario->id;

        $ehFornecedor = $usuario->fornecedor
            && (int) $negociacao->oferta->fornecedor_id
                === (int) $usuario->fornecedor->id;

        abort_unless($ehCliente || $ehFornecedor, 403);

        $temPropostaAceita = $negociacao->propostas()
            ->where('status', 'aceita')
            ->exists();

        if (!$temPropostaAceita) {
            return redirect()
                ->route('negociacoes.show', $negociacao)
                ->with('error', 'O chat será liberado quando uma proposta for aceita.');
        }

        $chat = Chat::firstOrCreate([
            'cliente_id' => $negociacao->cliente_id,
            'fornecedor_id' => $negociacao->oferta->fornecedor_id,
        ]);

        $assunto = $chat->assuntos()
            ->whereHas('negociacoes', function ($query) use ($negociacao) {
                $query->where('negociacoes.id', $negociacao->id);
            })
            ->first();

        if (!$assunto) {
            $assunto = $chat->assuntos()->create([
                'titulo' => 'Negociação #' . $negociacao->id,
            ]);

            $assunto->negociacoes()->attach($negociacao->id);
        }

        return redirect()->route('chat.show', $chat)
            ->with('assunto_chat_id', $assunto->id);
    }
}
