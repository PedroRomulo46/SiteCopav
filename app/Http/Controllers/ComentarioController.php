<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Negociacao;
use App\Models\Oferta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ComentarioController extends Controller
{
    public function store(Request $request, Oferta $oferta)
    {
        $usuario = $request->user();

        $clienteIdUsuario = $usuario?->cliente?->id ? (int) $usuario->cliente->id : null;
        $temColunaUserId = Schema::hasColumn('negociacoes', 'user_id');
        $temColunaUsuarioId = Schema::hasColumn('negociacoes', 'usuario_id');
        $temColunaClienteId = Schema::hasColumn('negociacoes', 'cliente_id');

        $comprouEFinalizouPagamento = $usuario && (
            $usuario->isAdmin()
            || Negociacao::where('oferta_id', $oferta->id)
                ->whereIn('status', ['pago', 'aprovado'])
                ->where(function ($query) use (
                    $usuario,
                    $clienteIdUsuario,
                    $temColunaUserId,
                    $temColunaUsuarioId,
                    $temColunaClienteId
                ) {
                    $temCondicao = false;

                    if ($temColunaUserId) {
                        $query->where('user_id', $usuario->id);
                        $temCondicao = true;
                    }

                    if ($temColunaUsuarioId) {
                        $metodo = $temCondicao ? 'orWhere' : 'where';
                        $query->{$metodo}('usuario_id', $usuario->id);
                        $temCondicao = true;
                    }

                    if ($temColunaClienteId && $clienteIdUsuario !== null) {
                        $metodo = $temCondicao ? 'orWhere' : 'where';
                        $query->{$metodo}('cliente_id', $clienteIdUsuario);
                        $temCondicao = true;
                    }

                    $metodoProposta = $temCondicao ? 'orWhereHas' : 'whereHas';
                    $query->{$metodoProposta}('propostas', function ($q) use ($usuario) {
                        $q->where('usuario_id', $usuario->id)
                            ->where('status', 'aceita');
                    });
                })
                ->exists()
        );

        if (! $comprouEFinalizouPagamento) {
            return redirect()
                ->to(route('ofertas.show', $oferta) . '#comentarios')
                ->with('error', 'Apenas compradores que concluíram o pagamento desta oferta podem publicar comentários.');
        }

        $dados = $request->validate([
            'conteudo' => ['required', 'string', 'min:2', 'max:1000'],
        ], [
            'conteudo.required' => 'Escreva um comentário antes de publicar.',
            'conteudo.min' => 'O comentário precisa ter pelo menos 2 caracteres.',
            'conteudo.max' => 'O comentário pode ter no máximo 1000 caracteres.',
        ]);

        Comentario::create([
            'oferta_id' => $oferta->id,
            'user_id' => $usuario->id,
            'conteudo' => $dados['conteudo'],
        ]);

        return redirect()
            ->to(route('ofertas.show', $oferta) . '#comentarios')
            ->with('success', 'Comentário publicado com sucesso!');
    }
}