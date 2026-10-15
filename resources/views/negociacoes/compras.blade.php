@extends('layouts.layout')

@section('title', 'Minhas Compras - Propostas Enviadas')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="w-full max-w-7xl mx-auto my-6 px-4 flex flex-col items-start text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 w-full text-left">
        <div class="text-left w-full">
            <p class="text-sm font-medium text-[#236350] mb-1 text-left">
                Painel do Comprador
            </p>
            <h1 class="text-3xl font-bold text-gray-800 text-left">
                Minhas Compras
            </h1>
            <p class="text-gray-500 mt-1 text-left">
                Acompanhe as propostas enviadas e o andamento das suas negociações com fornecedores.
            </p>
        </div>
    </div>

    {{-- Mensagens --}}
    @if(session('sucesso'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            {{ session('sucesso') }}
        </div>
    @endif

    @if(session('erro'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-lg">error</span>
            {{ session('erro') }}
        </div>
    @endif

    @if(isset($negociacoes) && $negociacoes->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 w-full text-left">

            @foreach($negociacoes as $negociacao)

                @php
                    $oferta = $negociacao->oferta;
                    $ultimaProposta = $negociacao->propostas->last();
                    $minhaPropostaAceita = $negociacao->propostas->where('status', 'aceita')->first();
                @endphp

                {{-- Card da Compra / Negociação --}}
                <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-left">

                    {{-- Imagem --}}
                    <div class="relative h-44 bg-gray-100 overflow-hidden">
                        <img
                            src="{{ $oferta->produto && $oferta->produto->imagem 
                                ? (str_contains($oferta->produto->imagem, 'assets/') 
                                    ? asset($oferta->produto->imagem) 
                                    : asset('storage/' . $oferta->produto->imagem))
                                : asset('assets/milho.png') }}"
                            alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        {{-- Badge de Status da Negociação --}}
                        <div class="absolute top-3 left-3">
                            @if($minhaPropostaAceita)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-600 text-white text-xs font-semibold shadow-sm">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    Proposta Aceita
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500 text-white text-xs font-semibold shadow-sm">
                                    <span class="material-symbols-outlined text-sm">pending</span>
                                    Em Negociação
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="p-5 text-left">

                        {{-- Produto e Fornecedor --}}
                        <div class="mb-4 text-left">
                            <h2 class="text-xl font-bold text-gray-800 text-left">
                                {{ $oferta->produto->nome }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1 text-left flex items-center gap-1">
                                <span class="material-symbols-outlined text-base">store</span>
                                Fornecedor: <strong>{{ $oferta->fornecedor->nome }}</strong>
                            </p>
                        </div>

                        {{-- Quantidade Solicitada e Valor Enviado --}}
                        @if($ultimaProposta)
                            <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 mb-4 text-left">
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400 mb-1">
                                    Sua última proposta
                                </p>
                                <p class="text-2xl font-bold text-[#236350]">
                                    R$ {{ number_format($ultimaProposta->valor, 2, ',', '.') }}
                                    <span class="text-xs text-gray-500 font-normal">/ {{ $oferta->unidade }}</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    Qtd. proposta: <strong>{{ number_format($ultimaProposta->quantidade ?? 0, 0, ',', '.') }} {{ $oferta->unidade }}</strong>
                                </p>
                            </div>
                        @endif

                        {{-- Botão de Acesso Direto para o Chat de Negociação --}}
                        <a
                            href="{{ route('negociacoes.show', $negociacao) }}"
                            class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-lg">forum</span>
                            Ver chat / negociação
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>

                    </div>
                </div>
            @endforeach
        </div>

    @else

        {{-- Estado Vazio para Compras --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm w-full">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 text-3xl">shopping_bag</span>
            </div>
            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhuma compra em andamento
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Você ainda não enviou propostas para ofertas. Explore os lotes disponíveis para iniciar uma negociação!
            </p>
            <a href="{{ route('ofertas.index') }}" class="btn-copav inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold mt-4 text-sm">
                Explorar Lotes
            </a>
        </div>

    @endif

</div>

@endsection