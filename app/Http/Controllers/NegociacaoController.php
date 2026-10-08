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
            })->with(['oferta.produto', 'oferta.fornecedor', 'cliente', 'propostas.usuario'])->get();
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

        // Apenas ADMIN ou o fornecedor dono da oferta
        // podem visualizar as propostas recebidas.
        if ($usuario->user_type !== 'admin') {

            if (
                !$usuario->fornecedor ||
                $oferta->fornecedor_id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        // Buscar negociações e propostas
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

        // Filtro por status
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

    // Busca a oferta independente do formato do status
    $oferta = Oferta::find($dados['oferta_id']);

    if (!$oferta) {
        return back()->with('error', 'Oferta não encontrada ou indisponível.');
    }

    $usuario = $request->user();

    // Impede o fornecedor dono do lote de negociar a própria oferta
    if (
        ($usuario->fornecedor && $oferta->fornecedor_id === $usuario->fornecedor->id) ||
        ($usuario->id === $oferta->user_id)
    ) {
        return back()->with('error', 'Você não pode negociar sua própria oferta.');
    }

    // Verifica se já existe uma negociação em aberto para este usuário e oferta
    $negociacaoExistente = Negociacao::where('oferta_id', $oferta->id)
        ->where('cliente_id', $usuario->id)
        ->first();

    if ($negociacaoExistente) {
        return redirect()
            ->route('negociacoes.show', $negociacaoExistente)
            ->with('success', 'Você já possui uma negociação ativa para esta oferta.');
    }

    // Cria a nova negociação
    $negociacao = Negociacao::create([
        'oferta_id'  => $oferta->id,
        'cliente_id' => $usuario->id,
        'status'     => 'em_negociacao',
    ]);

    $negociacao->load([
        'oferta.produto',
        'oferta.fornecedor',
        'cliente'
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
        'propostas.usuario'
    ]);

    // Admin pode ver qualquer negociação
    if ($usuario->user_type === 'admin') {
        return view('negociacoes.show', compact('negociacao'));
    }

    // Cliente só pode ver suas próprias negociações
    if ($negociacao->cliente_id === $usuario->id) {
        return view('negociacoes.show', compact('negociacao'));
    }

    // Fornecedor só pode ver negociações das suas ofertas
    if (
        $usuario->fornecedor &&
        $negociacao->oferta->fornecedor_id === $usuario->fornecedor->id
    ) {
        return view('negociacoes.show', compact('negociacao'));
    }

    abort(403);
    }

    public function visualizar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor'
        ]);

        $usuario = auth()->user();

        // ADMIN pode visualizar qualquer proposta
        if ($usuario->user_type !== 'admin') {

            // Somente o fornecedor dono da oferta pode visualizar
            if (
                !$usuario->fornecedor ||
                !$proposta->negociacao->oferta->fornecedor ||
                $proposta->negociacao->oferta->fornecedor->id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        // Marca somente esta proposta como visualizada
        if (!$proposta->visualizada_em) {
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

    //marcar como vizualizada pelo fornecedor
    public function marcarComoVisualizada(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        if ($usuario->user_type !== 'admin') {
            if (
                !$usuario->fornecedor ||
                $negociacao->oferta->fornecedor_id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        if (!$negociacao->fornecedor_visualizada_em) {
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
        $negociacao->delete();

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação excluída com sucesso!');
    }
}
