<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Negociacao;
use App\Models\Oferta;
use App\Models\Proposta;
use Illuminate\Http\Request;
use App\Events\PropostaCriada;
use App\Events\PropostaAtualizada;
use Illuminate\Support\Facades\DB;

class PropostaController extends Controller
{
    public function create(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
        ]);

        $usuario = auth()->user();

        // O dono da oferta não pode fazer proposta na própria oferta
        if (
            $negociacao->oferta->fornecedor &&
            (int) $negociacao->oferta->fornecedor->user_id === (int) $usuario->id
        ) {
            abort(403);
        }

        return view('propostas.create', compact('negociacao'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'negociacao_id' => 'required|exists:negociacoes,id',
            'valor'         => 'required|numeric|min:0',
            'quantidade'    => 'required|numeric|min:0.01',
            'observacao'    => 'nullable|string',
        ]);

        $negociacao = Negociacao::with([
            'oferta.fornecedor',
        ])->findOrFail($dados['negociacao_id']);

        $usuario = $request->user();

        // Dono da oferta não pode fazer proposta
        if (
            $negociacao->oferta->fornecedor &&
            (int) $negociacao->oferta->fornecedor->user_id === (int) $usuario->id
        ) {
            abort(403);
        }

        // Garante que o usuário realmente participa da negociação
        if ((int) $negociacao->cliente_id !== (int) $usuario->id) {
            abort(403);
        }

        $dados['usuario_id'] = $usuario->id;
        $dados['status'] = 'pendente';

        $proposta = Proposta::create($dados);

        $proposta->load([
            'negociacao.oferta.produto',
            'negociacao.oferta.fornecedor',
            'usuario',
        ]);

        event(new PropostaCriada($proposta));

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with('sucesso', 'Proposta enviada com sucesso!');
    }

    public function aceitar(Proposta $proposta)
    {
        $proposta->load('negociacao.oferta.fornecedor');

        $usuario = auth()->user();
        $negociacao = $proposta->negociacao;
        $oferta = $negociacao->oferta;

        $isAdmin = $usuario?->isAdmin() ?? false;
        $isDonoDaOferta = $oferta->fornecedor
            && (int) $oferta->fornecedor->user_id === (int) $usuario->id;

        if (! $isAdmin && ! $isDonoDaOferta) {
            abort(403);
        }

        if ($proposta->status !== 'pendente') {
            return back()->with(
                'erro',
                'Esta proposta já foi processada.'
            );
        }

        $resultado = DB::transaction(function () use ($proposta, $negociacao) {
            $ofertaBloqueada = Oferta::whereKey(
                $negociacao->oferta_id
            )->lockForUpdate()->firstOrFail();

            $propostaAtual = Proposta::whereKey($proposta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($propostaAtual->status !== 'pendente') {
                return [
                    'sucesso'  => false,
                    'mensagem' => 'Esta proposta já foi processada.',
                ];
            }

            $quantidadeAceita = Proposta::where('status', 'aceita')
                ->whereHas('negociacao', function ($query) use ($ofertaBloqueada) {
                    $query->where('oferta_id', $ofertaBloqueada->id);
                })
                ->sum('quantidade');

            $quantidadeDisponivel =
                (float) $ofertaBloqueada->quantidade
                - (float) $quantidadeAceita;

            if (
                (float) $propostaAtual->quantidade <= 0 ||
                (float) $propostaAtual->quantidade > $quantidadeDisponivel
            ) {
                return [
                    'sucesso'  => false,
                    'mensagem' => 'Quantidade insuficiente disponível para aceitar esta proposta.',
                ];
            }

            $propostaAtual->update([
                'status' => 'aceita',
            ]);

            $chat = Chat::firstOrCreate([
                'cliente_id'    => $negociacao->cliente_id,
                'fornecedor_id' => $ofertaBloqueada->fornecedor_id,
            ]);

            $assunto = $chat->assuntos()
                ->whereHas('negociacoes', function ($query) use ($negociacao) {
                    $query->where('negociacoes.id', $negociacao->id);
                })
                ->first();

            if (! $assunto) {
                $assunto = $chat->assuntos()->create([
                    'titulo' => 'Negociação #' . $negociacao->id,
                ]);

                $assunto->negociacoes()->attach($negociacao->id);
            }

            return [
                'sucesso'       => true,
                'proposta_id'   => $propostaAtual->id,
                'negociacao_id' => $propostaAtual->negociacao_id,
            ];
        });

        if (! $resultado['sucesso']) {
            return back()->with('erro', $resultado['mensagem']);
        }

        $propostaAtualizada = Proposta::findOrFail(
            $resultado['proposta_id']
        );

        event(new PropostaAtualizada($propostaAtualizada));

        return redirect()
            ->route('negociacoes.show', $resultado['negociacao_id'])
            ->with('sucesso', 'Proposta aceita e chat criado com sucesso!');
    }

    public function recusar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor',
        ]);

        $usuario = auth()->user();

        $isAdmin = $usuario?->isAdmin() ?? false;
        $isDonoDaOferta = $proposta->negociacao->oferta->fornecedor
            && (int) $proposta->negociacao->oferta->fornecedor->user_id === (int) $usuario->id;

        if (! $isAdmin && ! $isDonoDaOferta) {
            abort(403);
        }

        if ($proposta->status !== 'pendente') {
            return back()->with(
                'erro',
                'Esta proposta já foi processada.'
            );
        }

        $proposta->update([
            'status' => 'recusada',
        ]);

        $proposta->refresh();

        event(new PropostaAtualizada($proposta));

        return redirect()
            ->route('negociacoes.show', $proposta->negociacao)
            ->with('sucesso', 'Proposta recusada.');
    }

    public function cancelar(Proposta $proposta)
    {
        $proposta->load('negociacao');

        if ((int) $proposta->usuario_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($proposta->status !== 'pendente') {
            return back()->with(
                'erro',
                'Esta proposta já foi processada e não pode ser cancelada.'
            );
        }

        $proposta->update([
            'status' => 'cancelada',
        ]);

        $proposta->refresh();

        event(new PropostaAtualizada($proposta));

        return redirect()
            ->route('negociacoes.show', $proposta->negociacao)
            ->with('sucesso', 'Proposta cancelada com sucesso.');
    }

    public function visualizar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor',
        ]);

        $usuario = auth()->user();

        if (! $usuario?->isAdmin()) {
            if (
                ! $usuario?->isFornecedor() ||
                ! $proposta->negociacao->oferta->fornecedor ||
                (int) $proposta->negociacao->oferta->fornecedor->id !== (int) $usuario->fornecedor->id
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

    public function card(Proposta $proposta)
    {
        $proposta->load([
            'usuario',
            'negociacao.cliente',
            'negociacao.oferta',
        ]);

        $oferta = $proposta->negociacao->oferta;

        return view('negociacoes.partials.proposta-card', compact(
            'proposta',
            'oferta'
        ));
    }
}