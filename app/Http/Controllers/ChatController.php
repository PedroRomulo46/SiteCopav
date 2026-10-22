<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Negociacao;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function show(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->load([
            'cliente',
            'oferta.fornecedor',
        ]);

        // Verifica se o usuário é administrador.
        $ehAdmin = $usuario->user_type === 'admin';

        // Verifica se é o cliente da negociação.
        $ehCliente = (int) $negociacao->cliente_id === (int) $usuario->id;

        // Verifica se é o fornecedor dono da oferta.
        $ehFornecedor = $usuario->fornecedor
            && $negociacao->oferta
            && (int) $negociacao->oferta->fornecedor_id
                === (int) $usuario->fornecedor->id;

        // Somente os participantes autorizados podem acessar.
        if (!$ehAdmin && !$ehCliente && !$ehFornecedor) {
            abort(403);
        }

        // O chat só pode ser aberto quando existir uma proposta aceita.
        $temPropostaAceita = $negociacao->propostas()
            ->where('status', 'aceita')
            ->exists();

        if (!$temPropostaAceita) {
            return redirect()
                ->route('negociacoes.show', $negociacao)
                ->with('error', 'O chat será liberado quando uma proposta for aceita.');
        }

        // Cria o chat somente se ainda não existir.
        $chat = Chat::firstOrCreate([
            'negociacao_id' => $negociacao->id,
        ]);

        // Carrega as mensagens em ordem cronológica.
        $chat->load([
            'mensagens.usuario',
        ]);

        return view('chat.show', compact(
            'chat',
            'negociacao'
        ));
    }
}