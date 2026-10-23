
@extends('layouts.layout')

@section('title', 'Minhas Vendas')

@section('conteudo')
<nav class="text-sm text-gray-500 mb-5">
    <a href="{{ route('home') }}" class="hover:text-green-800">Início</a>
    <span class="mx-2">›</span>
    <span class="font-semibold text-gray-800">Minhas Vendas</span>
</nav>

<div class="w-full max-w-7xl mx-auto px-4 py-6">

    <div class="mb-8">
        <p class="text-sm font-semibold text-[#236350] mb-1">
            Painel do Vendedor
        </p>
        <h1 class="text-3xl font-bold text-gray-800">
            Minhas Vendas
        </h1>
        <p class="text-gray-500 mt-2">
            Acompanhe as negociações recebidas nos seus lotes.
        </p>
    </div>

    @if(session('sucesso'))
        <div class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('sucesso') }}
        </div>
    @endif

    @if($negociacoes->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($negociacoes as $negociacao)
                @php
                    $oferta = $negociacao->oferta;
                    $produto = $oferta?->produto;
                    $ultimaProposta = $negociacao->propostas->sortBy('created_at')->last();
                @endphp

                <article class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">

                    <div class="h-44 bg-gray-100">
                        <img
                            src="{{ $produto && $produto->imagem
                                ? (str_contains($produto->imagem, 'assets/')
                                    ? asset($produto->imagem)
                                    : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
                                : asset('assets/milho.png') }}"
                            alt="{{ $produto->nome ?? 'Produto' }}"
                            class="w-full h-full object-cover"
                        >
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $produto->nome ?? 'Produto indisponível' }}
                        </h2>

                        <p class="text-sm text-gray-500 mt-2">
                            Comprador:
                            <strong class="text-gray-700">
                                {{ $negociacao->cliente->nome
                                    ?? $negociacao->cliente->name
                                    ?? 'Usuário' }}
                            </strong>
                        </p>

                        <div class="mt-4 mb-5 rounded-xl bg-gray-50 border border-gray-100 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">
                                Última proposta
                            </p>

                            @if($ultimaProposta)
                                <p class="text-2xl font-bold text-[#236350] mt-1">
                                    R$ {{ number_format($ultimaProposta->valor, 2, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Quantidade:
                                    <strong>
                                        {{ number_format($ultimaProposta->quantidade ?? 0, 0, ',', '.') }}
                                        {{ $oferta->unidade ?? '' }}
                                    </strong>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Situação: <strong> {{ ucfirst(str_replace('_', ' ', $ultimaProposta->status)) }} </strong>
                                </p>
                            @else
                                <p class="text-sm text-gray-500 mt-2">
                                    Ainda não há propostas nesta negociação.
                                </p>
                            @endif
                        </div>

                        {{-- Botão de negociação --}}
                        <div>
                            <a
                                href="{{ route('negociacoes.show', $negociacao) }}"
                                class="btn-copav w-full rounded-lg px-4 py-3 gap-2 text-sm font-semibold"
                            >
                                <span class="material-symbols-outlined">library_books</span>
                                Ver Negociação
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-8 sm:p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-5xl text-gray-400">
                storefront
            </span>
            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhuma venda em andamento
            </h2>
            <p class="text-gray-500 mt-2">
                Quando alguém iniciar uma negociação nos seus lotes, ela aparecerá aqui.
            </p>
            <a
                href="{{ route('ofertas.index') }}"
                class="btn-copav rounded-lg px-5 py-3 mt-5"
            >
                Ver lotes
            </a>
        </div>
    @endif
</div>
@endsection