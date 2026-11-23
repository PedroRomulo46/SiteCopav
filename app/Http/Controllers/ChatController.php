<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Mensagem;
use App\Models\Negociacao;
use App\Models\AssuntoChat;
use App\Events\MensagemEnviada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Página principal do chat.
     */
    public function index()
    {
        $usuario = auth()->user();

        $query = Chat::query()
            ->with([
                'cliente',
                'fornecedor.usuario',
                'assuntos.negociacoes',
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
            $consultaGerais = $chat->mensagens()
                ->whereNull('assunto_chat_id')
                ->where('usuario_id', '!=', $usuario->id);

            $ultimaLeituraGeral = $chat->$campoLeitura;

            if ($ultimaLeituraGeral !== null) {
                $consultaGerais->where('id', '>', $ultimaLeituraGeral);
            }

            $naoLidasGerais = (clone $consultaGerais)->count();

            $ultimaMensagemGeral = $chat->mensagens()
                ->whereNull('assunto_chat_id')
                ->latest('id')
                ->first();

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
                                ? asset('storage/' . ltrim($produto->imagem, '/'))
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
                        ->map(function ($negociacao) {
                            $produto = $negociacao->oferta?->produto;

                            if (!$produto) {
                                return null;
                            }

                            return [
                                'id' => $negociacao->id,
                                'produto' => $produto->nome,
                                'url' => route('ofertas.show', $negociacao->oferta),
                            ];
                        })
                        ->filter()
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
            * ÚLTIMA MENSAGEM GERAL OU DE ASSUNTO
            */
            $ultimaMensagem = $chat->mensagens->first();

            return [
                'chat_id' => $chat->id,

                'participante' => $participante ?? 'Participante',

                'mensagens_gerais' => [
                    'assunto_chat_id' => null,

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

        if ($assuntoId !== null) {
            $chat->assuntos()->findOrFail($assuntoId);
        }

        $this->marcarComoLida($chat, $usuario, $assuntoId);

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
