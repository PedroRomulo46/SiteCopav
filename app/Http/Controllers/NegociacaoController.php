<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Oferta;
use App\Models\User;
use App\Models\Proposta;
use Illuminate\Http\Request;
use App\Events\NegociacaoCriada;

class NegociacaoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if ($usuario->isAdmin()) {
            $negociacoes = Negociacao::with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario',
            ])->get();
        } else {
            $negociacoes = Negociacao::where(function ($query) use ($usuario) {
                $query->where('cliente_id', $usuario->id);

                if ($usuario->isFornecedor()) {
                    $query->orWhereHas('oferta', function ($subQuery) use ($usuario) {
                        $subQuery->where('fornecedor_id', $usuario->fornecedor->id);
                    });
                }
            })->with(['oferta.produto', 'oferta.fornecedor', 'cliente', 'propostas.usuario'])->get();
        }

        $ofertas = $negociacoes->groupBy('oferta_id');

        return view('negociacoes.index', compact('ofertas'));
    }

    public function compras()
    {
        $usuario = auth()->user();

        $negociacoes = Negociacao::where('cliente_id', $usuario->id)
            ->whereHas('propostas')
            ->with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario',
            ])
            ->latest()
            ->get();

        return view('negociacoes.compras', compact('negociacoes'));
    }

    public function vendas()
    {
        $usuario = auth()->user();

        if (! $usuario->isFornecedor() && ! $usuario->isAdmin()) {
            abort(403);
        }

        $consulta = Negociacao::query();

        if ($usuario->isFornecedor()) {
            $fornecedorId = $usuario->fornecedor->id;
            $consulta->whereHas('oferta', function ($query) use ($fornecedorId) {
                $query->where('fornecedor_id', $fornecedorId);
            });
        } else {
            $consulta->whereRaw('1 = 0');
        }

        $negociacoes = $consulta
            ->with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario',
            ])
            ->latest()
            ->get();

        return view('negociacoes.vendas', compact('negociacoes'));
    }

    public function create()
    {
        $ofertas = Oferta::with(['produto', 'fornecedor'])
            ->where('status', 'publicada')
            ->get();

        return view('negociacoes.create', compact('ofertas'));
    }

    public function propostasOferta(Request $request, Oferta $oferta)
    {
        $usuario = auth()->user();

        if (! $usuario->isAdmin()) {
            if (
                ! $usuario->isFornecedor() ||
                $oferta->fornecedor_id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        $negociacoes = Negociacao::where('oferta_id', $oferta->id)
            ->with([
                'cliente',
                'propostas.usuario',
            ])
            ->get();

        $propostas = $negociacoes
            ->flatMap(function ($negociacao) {
                return $negociacao->propostas->map(function ($proposta) use ($negociacao) {
                    $proposta->cliente = $negociacao->cliente;
                    $proposta->negociacao_id = $negociacao->id;

                    return $proposta;
                });
            });

        $status = $request->query('status', 'todas');

        if (in_array($status, [
            'pendente',
            'aceita',
            'recusada',
            'cancelada',
        ])) {
            $propostas = $propostas->where('status', $status);
        }

        $ordenar = $request->query('ordenar', 'valor_total');

        switch ($ordenar) {
            case 'valor_unidade':
                $propostas = $propostas->sortByDesc(function ($proposta) {
                    return $proposta->valor;
                });
                break;

            case 'quantidade':
                $propostas = $propostas->sortByDesc(function ($proposta) {
                    return $proposta->quantidade ?? 0;
                });
                break;

            case 'recentes':
                $propostas = $propostas->sortByDesc(function ($proposta) {
                    return $proposta->created_at;
                });
                break;

            case 'valor_total':
            default:
                $propostas = $propostas->sortByDesc(function ($proposta) {
                    return $proposta->valor * ($proposta->quantidade ?? 0);
                });
                break;
        }

        $propostas = $propostas->values();

        $todasPropostas = $negociacoes->flatMap(function ($negociacao) {
            return $negociacao->propostas;
        });

        $melhorValor = $todasPropostas->max('valor');

        $maiorValorTotal = $todasPropostas->max(function ($proposta) {
            return $proposta->valor * ($proposta->quantidade ?? 0);
        });

        $quantidadeAceita = $todasPropostas
            ->where('status', 'aceita')
            ->sum(function ($proposta) {
                return $proposta->quantidade ?? 0;
            });

        $totalPropostas = $todasPropostas->count();
        $pendentes = $todasPropostas->where('status', 'pendente')->count();
        $aceitas = $todasPropostas->where('status', 'aceita')->count();
        $recusadas = $todasPropostas->where('status', 'recusada')->count();

        return view('negociacoes.propostas', compact(
            'oferta',
            'negociacoes',
            'propostas',
            'melhorValor',
            'maiorValorTotal',
            'quantidadeAceita',
            'status',
            'ordenar',
            'totalPropostas',
            'pendentes',
            'aceitas',
            'recusadas'
        ));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'oferta_id' => 'required|exists:ofertas,id',
        ]);

        $oferta = Oferta::find($dados['oferta_id']);

        if (! $oferta) {
            return back()->with('error', 'Oferta não encontrada ou indisponível.');
        }

        $usuario = $request->user();

        if (
            ! $usuario->isAdmin()
            && $usuario->isFornecedor()
            && (int) $oferta->fornecedor_id === (int) $usuario->fornecedor->id
        ) {
            return back()->with('error', 'Você não pode negociar sua própria oferta.');
        }

        $negociacaoExistente = Negociacao::where('oferta_id', $oferta->id)
            ->where('cliente_id', $usuario->id)
            ->first();

        if ($negociacaoExistente) {
            return redirect()
                ->route('negociacoes.show', $negociacaoExistente)
                ->with('success', 'Você já possui uma negociação ativa para esta oferta.');
        }

        $negociacao = Negociacao::create([
            'oferta_id'  => $oferta->id,
            'cliente_id' => $usuario->id,
            'status'     => 'em_negociacao',
        ]);

        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
        ]);

        event(new NegociacaoCriada($negociacao));

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with('success', 'Negociação iniciada com sucesso!');
    }

    public function show(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
            'propostas.usuario',
        ]);

        if ($usuario->isAdmin()) {
            return view('negociacoes.show', compact('negociacao'));
        }

        if ($negociacao->cliente_id === $usuario->id) {
            return view('negociacoes.show', compact('negociacao'));
        }

        if (
            $usuario->isFornecedor() &&
            $negociacao->oferta->fornecedor_id === $usuario->fornecedor->id
        ) {
            return view('negociacoes.show', compact('negociacao'));
        }

        abort(403);
    }

    public function visualizar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor',
        ]);

        $usuario = auth()->user();

        if (! $usuario->isAdmin()) {
            if (
                ! $usuario->isFornecedor() ||
                ! $proposta->negociacao->oferta->fornecedor ||
                $proposta->negociacao->oferta->fornecedor->id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        if (! $proposta->visualizada_em) {
            $proposta->update([
                'visualizada_em' => now(),
            ]);
        }

        return redirect()->route(
            'negociacoes.show',
            $proposta->negociacao_id
        );
    }

    public function edit(Negociacao $negociacao)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $ofertas = Oferta::where('status', 'publicada')->get();
        $clientes = User::where('user_type', 'cliente')->get();

        return view('negociacoes.edit', compact('negociacao', 'ofertas', 'clientes'));
    }

    public function update(Request $request, Negociacao $negociacao)
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $dados = $request->validate([
            'oferta_id'  => 'required|exists:ofertas,id',
            'cliente_id' => 'required|exists:users,id',
            'status'     => 'required|in:pendente,em_negociacao,aceita,recusada,concluida,cancelada',
        ]);

        $negociacao->update($dados);

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação atualizada com sucesso!');
    }

    public function marcarComoVisualizada(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        if (! $usuario->isAdmin()) {
            if (
                ! $usuario->isFornecedor() ||
                $negociacao->oferta->fornecedor_id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        if (! $negociacao->fornecedor_visualizada_em) {
            $negociacao->update([
                'fornecedor_visualizada_em' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Negociacao $negociacao)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $negociacao->delete();

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação excluída com sucesso!');
    }
}