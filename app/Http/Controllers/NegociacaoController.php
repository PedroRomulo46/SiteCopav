<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Oferta;
use App\Models\User;
use Illuminate\Http\Request;

class NegociacaoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        // ADMIN pode visualizar todas as negociações
        if ($usuario->user_type === 'admin') {

            $negociacoes = Negociacao::with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario'
            ])->get();

        } else {

            $negociacoes = Negociacao::where(function ($query) use ($usuario) {

                // Negociações que o usuário iniciou como cliente
                $query->where('cliente_id', $usuario->id);

                // OU negociações relacionadas às ofertas
                // pertencentes ao fornecedor desse usuário
                if ($usuario->fornecedor) {

                    $query->orWhereHas('oferta', function ($subQuery) use ($usuario) {

                        $subQuery->where(
                            'fornecedor_id',
                            $usuario->fornecedor->id
                        );

                    });
                }

            })
            ->with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario'
            ])
            ->get();
        }

        // Agrupa as negociações pela oferta
        $ofertas = $negociacoes->groupBy('oferta_id');

        return view('negociacoes.index', compact('ofertas'));
    }

    public function create()
    {
        $ofertas = Oferta::with(['produto', 'fornecedor'])
            ->where('status', 'publicada')
            ->get();

        return view(
            'negociacoes.create',
            compact('ofertas')
        );
    }
    

    public function propostasOferta(Request $request, Oferta $oferta)
    {
        $usuario = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Verificar se o usuário pode visualizar as propostas
        |--------------------------------------------------------------------------
        */

        // ADMIN pode visualizar tudo
        if ($usuario->user_type !== 'admin') {

            $podeVisualizar = false;

            // É o fornecedor dono da oferta?
            if (
                $usuario->fornecedor &&
                $oferta->fornecedor_id === $usuario->fornecedor->id
            ) {
                $podeVisualizar = true;
            }

            // Participa de alguma negociação dessa oferta?
            if (
                Negociacao::where('oferta_id', $oferta->id)
                    ->where('cliente_id', $usuario->id)
                    ->exists()
            ) {
                $podeVisualizar = true;
            }

            if (!$podeVisualizar) {
                abort(403);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Buscar negociações e propostas
        |--------------------------------------------------------------------------
        */

        $negociacoes = Negociacao::where('oferta_id', $oferta->id)
            ->with([
                'cliente',
                'propostas.usuario'
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

        /*
        |--------------------------------------------------------------------------
        | Filtro por status
        |--------------------------------------------------------------------------
        */

        $status = $request->query('status', 'todas');

        if (in_array($status, [
            'pendente',
            'aceita',
            'recusada'
        ])) {
            $propostas = $propostas->where('status', $status);
        }

        /*
        |--------------------------------------------------------------------------
        | Ordenação
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Estatísticas
        |--------------------------------------------------------------------------
        */

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

        $pendentes = $todasPropostas
            ->where('status', 'pendente')
            ->count();

        $aceitas = $todasPropostas
            ->where('status', 'aceita')
            ->count();

        $recusadas = $todasPropostas
            ->where('status', 'recusada')
            ->count();

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

        $oferta = Oferta::where('id', $dados['oferta_id'])
            ->where('status', 'publicada')
            ->first();

        if (!$oferta) {
            abort(403);
        }

        $usuario = $request->user();

        // Impede o fornecedor de negociar a própria oferta
        if (
            $usuario->fornecedor &&
            $oferta->fornecedor_id === $usuario->fornecedor->id
        ) {
            return back()->with(
                'erro',
                'Você não pode negociar sua própria oferta.'
            );
        }

        // Verifica se já existe uma negociação para essa oferta
        $negociacaoExistente = Negociacao::where('oferta_id', $oferta->id)
            ->where('cliente_id', $usuario->id)
            ->first();

        if ($negociacaoExistente) {
            return redirect()
                ->route('negociacoes.show', $negociacaoExistente)
                ->with(
                    'sucesso',
                    'Você já possui uma negociação para esta oferta.'
                );
        }

        $negociacao = Negociacao::create([
            'oferta_id' => $oferta->id,
            'cliente_id' => $usuario->id,
            'status' => 'em_negociacao',
        ]);

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with(
                'sucesso',
                'Negociação iniciada com sucesso!'
            );
    }

    public function show(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
            'propostas.usuario'
        ]);

        return view('negociacoes.show', compact('negociacao'));
    }

    public function edit(Negociacao $negociacao)
    {
        $ofertas = Oferta::where('status', 'publicada')->get();
        $clientes = User::where('user_type', 'cliente')->get();

        return view('negociacoes.edit', compact('negociacao', 'ofertas', 'clientes'));
    }

    public function update(Request $request, Negociacao $negociacao) {
        $dados = $request->validate([
            'oferta_id' => 'required|exists:ofertas,id',
            'cliente_id' => 'required|exists:users,id',
            'status' => 'required|in:pendente,em_negociacao,aceita,recusada,concluida,cancelada',
        ]);

        $negociacao->update($dados);

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação atualizada com sucesso!');
    }

    public function destroy(Negociacao $negociacao)
    {
        $negociacao->delete();

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação excluída com sucesso!');
    }
}