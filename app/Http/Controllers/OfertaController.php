<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Produto;
use App\Events\OfertaCriada;
use App\Events\OfertaExcluida;
use App\Events\OfertaAtualizada;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfertaController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if ($usuario?->isAdmin()) {
            $ofertas = Oferta::with(['fornecedor', 'produto'])->get();
        } elseif ($usuario?->isFornecedor()) {
            $ofertas = Oferta::with(['fornecedor', 'produto'])
                ->where('fornecedor_id', $usuario->fornecedor->id)
                ->get();
        } else {
            // Cliente ou visitante: exibe ofertas publicadas (ou ajuste conforme a regra da vitrine)
            $ofertas = Oferta::with(['fornecedor', 'produto'])
                ->where('status', 'publicada')
                ->get();
        }

        return view('ofertas.index', compact('ofertas'));
    }

    public function create()
    {
        $usuario = auth()->user();
        $fornecedor = $usuario?->fornecedor;

        if (! $fornecedor) {
            return redirect()
                ->route('fornecedores.create')
                ->with(
                    'sucesso',
                    'Você precisa cadastrar um fornecedor antes de cadastrar ofertas.'
                );
        }

        if ($fornecedor->status !== 'ativo') {
            return redirect()
                ->route('fornecedores.show', $fornecedor)
                ->with(
                    'sucesso',
                    'Seu fornecedor ainda não está ativo.'
                );
        }

        $produtos = Produto::where('fornecedor_id', $fornecedor->id)
            ->with('categoria')
            ->orderBy('nome')
            ->get();

        return view('ofertas.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $usuario = $request->user();
        $fornecedor = $usuario?->fornecedor;

        if (! $usuario?->isFornecedor() || ! $fornecedor || $fornecedor->status !== 'ativo') {
            abort(403);
        }

        $dados = $request->validate([
            'produto_id' => [
                'required',
                Rule::exists('produtos', 'id')
                    ->where(function ($query) use ($fornecedor) {
                        $query->where('fornecedor_id', $fornecedor->id);
                    }),
            ],
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        $dados['fornecedor_id'] = $fornecedor->id;

        $oferta = Oferta::create($dados);

        event(new OfertaCriada($oferta));

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta cadastrada com sucesso!');
    }

    public function show(Oferta $oferta)
    {
        $oferta->load([
            'fornecedor',
            'produto',
        ]);

        $produto = $oferta->produto;

        return view('ofertas.show', compact('oferta', 'produto'));
    }

    public function edit(Oferta $oferta)
    {
        $usuario = auth()->user();
        $fornecedor = $usuario?->fornecedor;
        $isAdmin = $usuario?->isAdmin() ?? false;

        $donoDaOferta = $fornecedor && $oferta->fornecedor_id === $fornecedor->id;

        if (! $donoDaOferta && ! $isAdmin) {
            abort(403);
        }

        $produtos = $isAdmin
            ? Produto::with('categoria')->orderBy('nome')->get()
            : Produto::where('fornecedor_id', $fornecedor->id)->with('categoria')->orderBy('nome')->get();

        return view('ofertas.edit', compact('oferta', 'produtos'));
    }

    public function update(Request $request, Oferta $oferta)
    {
        $usuario = $request->user();
        $fornecedor = $usuario?->fornecedor;
        $isAdmin = $usuario?->isAdmin() ?? false;

        $donoDaOferta = $fornecedor && $oferta->fornecedor_id === $fornecedor->id;

        if (! $donoDaOferta && ! $isAdmin) {
            abort(403);
        }

        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        if ($dados['status'] === 'publicada') {
            $dados['data_inicio'] = now()->toDateString();
        }

        if (! $isAdmin) {
            $produtoPertenceAoFornecedor = Produto::where('id', $dados['produto_id'])
                ->where('fornecedor_id', $fornecedor->id)
                ->exists();

            if (! $produtoPertenceAoFornecedor) {
                abort(403);
            }
        }

        $oferta->update($dados);
        $oferta->refresh();

        event(new OfertaAtualizada($oferta));

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function destroy(Oferta $oferta)
    {
        $usuario = auth()->user();
        $fornecedor = $usuario?->fornecedor;
        $isAdmin = $usuario?->isAdmin() ?? false;

        $donoDaOferta = $fornecedor && $oferta->fornecedor_id === $fornecedor->id;

        if (! $donoDaOferta && ! $isAdmin) {
            abort(403);
        }

        $ofertaId = $oferta->id;

        $oferta->delete();

        event(new OfertaExcluida($ofertaId));

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta excluída com sucesso!');
    }

    public function card(Oferta $oferta)
    {
        $oferta->load([
            'fornecedor',
            'produto',
        ]);

        return view('ofertas.partials.card', compact('oferta'));
    }
}