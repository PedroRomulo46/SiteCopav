<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Negociacao;
use App\Models\Mensagem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\MensagemEnviada;

class ChatController extends Controller
{

    public function index()
    {
        $usuario = auth()->user();

        $chats = \App\Models\Chat::query()
            ->whereHas('negociacao', function ($query) use ($usuario) {
                $query->where(function ($subQuery) use ($usuario) {
                    $subQuery->where('cliente_id', $usuario->id)
                        ->orWhereHas('oferta', function ($ofertaQuery) use ($usuario) {
                            if ($usuario->fornecedor) {
                                $ofertaQuery->where(
                                    'fornecedor_id',
                                    $usuario->fornecedor->id
                                );
                            } else {
                                $ofertaQuery->whereRaw('1 = 0');
                            }
                        });
                });
            })
            ->whereHas('negociacao.propostas', function ($query) {
                $query->where('status', 'aceita');
            })
            ->with([
                'negociacao.cliente',
                'negociacao.oferta.produto',
                'negociacao.oferta.fornecedor',
                'mensagens' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->orderByDesc('ultima_mensagem_em')
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function conversas()
    {
        $usuario = auth()->user();

        $chats = Chat::query()
            ->whereHas('negociacao', function ($query) use ($usuario) {
                $query->where(function ($subQuery) use ($usuario) {
                    $subQuery->where('cliente_id', $usuario->id)
                        ->orWhereHas('oferta', function ($ofertaQuery) use ($usuario) {
                            if ($usuario->fornecedor) {
                                $ofertaQuery->where(
                                    'fornecedor_id',
                                    $usuario->fornecedor->id
                                );
                            } else {
                                $ofertaQuery->whereRaw('1 = 0');
                            }
                        });
                });
            })
            ->whereHas('negociacao.propostas', function ($query) {
                $query->where('status', 'aceita');
            })
            ->with([
                'negociacao.cliente',
                'negociacao.oferta.fornecedor',
                'mensagens' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->withCount([
                'mensagens as nao_lidas_cliente' => function ($query) use ($usuario) {
                    $query->where('mensagems.usuario_id', '!=', $usuario->id)
                        ->where(function ($subQuery) {
                            $subQuery->whereNull('chats.cliente_ultima_leitura_id')
                                ->orWhereColumn(
                                    'mensagems.id',
                                    '>',
                                    'chats.cliente_ultima_leitura_id'
                                );
                        });
                },

                'mensagens as nao_lidas_fornecedor' => function ($query) use ($usuario) {
                    $query->where('mensagems.usuario_id', '!=', $usuario->id)
                        ->where(function ($subQuery) {
                            $subQuery->whereNull('chats.fornecedor_ultima_leitura_id')
                                ->orWhereColumn(
                                    'mensagems.id',
                                    '>',
                                    'chats.fornecedor_ultima_leitura_id'
                                );
                        });
                },
            ])
            ->orderByDesc('ultima_mensagem_em')
            ->get();

        $conversas = $chats->map(function ($chat) use ($usuario) {
            $negociacao = $chat->negociacao;

            $ehCliente = (int) $negociacao->cliente_id === (int) $usuario->id;

            $participante = $ehCliente
                ? $negociacao->oferta->fornecedor->nome
                : $negociacao->cliente->nome;

            $ultimaMensagem = $chat->mensagens->first();

            return [
                'chat_id' => $chat->id,
                'negociacao_id' => $negociacao->id,
                'participante' => $participante,
                'ultima_mensagem' => $ultimaMensagem?->conteudo
                    ?? 'Nenhuma mensagem ainda',
                'mensagens_nao_lidas' => (int) (
                    $ehCliente
                        ? $chat->nao_lidas_cliente
                        : $chat->nao_lidas_fornecedor
                ),
                'url' => route('chat.show', $negociacao->id),
            ];
        });

        return response()->json($conversas);
    }

        private function marcarComoLida(
            Chat $chat,
            Negociacao $negociacao,
            $usuario
        ): void {
            $ultimaMensagemId = $chat->mensagens()->max('id');

            if (!$ultimaMensagemId) {
                return;
            }

            $ehCliente = (int) $negociacao->cliente_id === (int) $usuario->id;

            $campo = $ehCliente
                ? 'cliente_ultima_leitura_id'
                : 'fornecedor_ultima_leitura_id';

            $chat->forceFill([
                $campo => $ultimaMensagemId,
            ])->save();
        }

    public function show(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->load([
            'cliente',
            'oferta.fornecedor',
            'propostas',
        ]);

        // Cliente que iniciou a negociação.
        $ehCliente = (int) $negociacao->cliente_id
            === (int) $usuario->id;

        // Fornecedor responsável pela oferta.
        $ehFornecedor = $usuario->fornecedor
            && $negociacao->oferta
            && (int) $negociacao->oferta->fornecedor_id
                === (int) $usuario->fornecedor->id;

        // Somente os participantes podem acessar o chat.
        if (!$ehCliente && !$ehFornecedor) {
            abort(403);
        }

        // O chat só é liberado após uma proposta aceita.
        $temPropostaAceita = $negociacao->propostas
            ->contains('status', 'aceita');

        if (!$temPropostaAceita) {
            return redirect()
                ->route('negociacoes.show', $negociacao)
                ->with(
                    'error',
                    'O chat será liberado quando uma proposta for aceita.'
                );
        }

        // Garante um único chat por negociação.
        $chat = Chat::firstOrCreate([
            'negociacao_id' => $negociacao->id,
        ]);

        $chat->load([
            'mensagens.usuario',
        ]);
        $this->marcarComoLida($chat, $negociacao, $usuario);

        return view('chat.show', compact(
            'chat',
            'negociacao'
        ));
    }

    

    public function mensagens(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->load('oferta.fornecedor');

        $ehCliente = (int) $negociacao->cliente_id === (int) $usuario->id;

        $ehFornecedor = $usuario->fornecedor
            && $negociacao->oferta
            && (int) $negociacao->oferta->fornecedor_id === (int) $usuario->fornecedor->id;

        if (!$ehCliente && !$ehFornecedor) {
            abort(403);
        }

        $temPropostaAceita = $negociacao->propostas()
            ->where('status', 'aceita')
            ->exists();

        if (!$temPropostaAceita) {
            return response()->json([
                'message' => 'O chat exige uma proposta aceita.'
            ], 403);
        }

        $chat = Chat::where('negociacao_id', $negociacao->id)
            ->firstOrFail();
        $this->marcarComoLida($chat, $negociacao, $usuario);

        $mensagens = $chat->mensagens()
            ->with('usuario:id,nome')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($mensagem) {
                return [
                    'id' => $mensagem->id,
                    'usuario_id' => $mensagem->usuario_id,
                    'nome' => $mensagem->usuario->nome ?? 'Participante',
                    'conteudo' => $mensagem->conteudo,
                    'created_at' => $mensagem->created_at->utc()->toISOString(),
                ];
            });

        return response()->json([
            'chat_id' => $chat->id,
            'mensagens' => $mensagens
        ]);
    }

    public function store(Request $request, Negociacao $negociacao)
    {
        $usuario = $request->user();

        $negociacao->load('oferta.fornecedor');

        $ehCliente = (int) $negociacao->cliente_id
            === (int) $usuario->id;

        $ehFornecedor = $usuario->fornecedor
            && $negociacao->oferta
            && (int) $negociacao->oferta->fornecedor_id
                === (int) $usuario->fornecedor->id;

        if (!$ehCliente && !$ehFornecedor) {
            abort(403);
        }

        $temPropostaAceita = $negociacao->propostas()
            ->where('status', 'aceita')
            ->exists();

        if (!$temPropostaAceita) {
            return response()->json([
                'message' => 'O chat exige uma proposta aceita.',
            ], 403);
        }

        $dados = $request->validate([
            'conteudo' => ['required', 'string', 'max:5000'],
        ]);

        $chat = \App\Models\Chat::firstOrCreate([
            'negociacao_id' => $negociacao->id,
        ]);

        $mensagem = DB::transaction(function () use (
            $chat,
            $usuario,
            $dados
        ) {
            $mensagem = Mensagem::create([
                'chat_id' => $chat->id,
                'usuario_id' => $usuario->id,
                'conteudo' => trim($dados['conteudo']),
            ]);

            $chat->update([
                'ultima_mensagem_em' => $mensagem->created_at,
            ]);

            return $mensagem;
        });

        broadcast(new MensagemEnviada($mensagem))->toOthers();
        
        return response()->json([
            'success' => true,
            'mensagem' => [
                'id' => $mensagem->id,
                'usuario_id' => $mensagem->usuario_id,
                'nome' => $usuario->nome,
                'conteudo' => $mensagem->conteudo,
                'created_at' => $mensagem->created_at->format('d/m/Y H:i'),
            ],
        ], 201);
    }
}