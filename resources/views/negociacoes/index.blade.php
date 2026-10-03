@extends('layouts.layout')

@section('title', 'Minhas Negociações')

@section('conteudo')

{{-- Botão de Voltar --}}
<div class="mb-4">
    <a
        href="{{ route('home') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-7xl mx-auto my-8 px-4">

    {{-- CABEÇALHO --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-medium text-[#236350] mb-1">
                Painel de negociações
            </p>
            <h1 class="text-3xl font-bold text-gray-800">
                Minhas ofertas
            </h1>
            <p class="text-gray-500 mt-1">
                Acompanhe as propostas recebidas em suas ofertas.
            </p>
        </div>

    </div>

    {{-- MENSAGENS --}}
    @if(session('sucesso'))

        <div class="mb-5 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">

            <span class="material-symbols-outlined text-lg">
                check_circle
            </span>

            {{ session('sucesso') }}

        </div>

    @endif

    @if(session('erro'))

        <div class="mb-5 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">

            <span class="material-symbols-outlined text-lg">
                error
            </span>

            {{ session('erro') }}

        </div>

    @endif

    @if($ofertas->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

            @foreach($ofertas as $ofertaId => $negociacoes)

                @php

                    $oferta = $negociacoes->first()->oferta;

                    $propostas = $negociacoes->flatMap(function ($negociacao) {
                        return $negociacao->propostas;
                    });

                    $melhorValor = $propostas->max('valor');

                    $maiorValorTotal = $propostas->max(function ($proposta) {
                        return $proposta->valor * ($proposta->quantidade ?? 0);
                    });

                    $quantidadeAceita = $propostas
                        ->where('status', 'aceita')
                        ->sum(function ($proposta) {
                            return $proposta->quantidade ?? 0;
                        });

                    $percentualAceito = $oferta->quantidade > 0
                        ? min(($quantidadeAceita / $oferta->quantidade) * 100, 100)
                        : 0;

                @endphp

                {{-- CARD DA OFERTA --}}
                <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

                    {{-- IMAGEM --}}
                    <div class="relative h-44 bg-gray-100 overflow-hidden">

                        <img
                            src="{{ $oferta->produto && $oferta->produto->imagem 
                                ? (str_contains($oferta->produto->imagem, 'assets/') 
                                    ? asset($oferta->produto->imagem) 
                                    : asset('storage/' . $oferta->produto->imagem))
                                : asset('assets/milho.png') }}"
                            alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        {{-- NÚMERO DA OFERTA --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-sm text-xs font-semibold text-gray-700 shadow-sm">
                                Oferta #{{ $oferta->id }}
                            </span>
                        </div>

                        {{-- NÚMERO DE NEGOCIAÇÕES --}}
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#236350]/95 text-white text-xs font-medium shadow-sm">
                                <span class="material-symbols-outlined text-sm">
                                    forum
                                </span>
                                {{ $negociacoes->count() }}
                            </span>
                        </div>
                    </div>

                    {{-- CONTEÚDO --}}
                    <div class="p-5">

                        {{-- PRODUTO --}}
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $oferta->produto->nome }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $oferta->fornecedor->nome }}
                            </p>
                        </div>

                        {{-- QUANTIDADE --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400">
                                    Oferta
                                </p>
                                <p class="font-semibold text-gray-800 mt-0.5">
                                    @if(strtolower($oferta->unidade) === 'saca')
                                        {{ number_format($oferta->quantidade, 0, ',', '.') }}
                                    @else
                                        {{ fmod($oferta->quantidade, 1) == 0
                                            ? number_format($oferta->quantidade, 0, ',', '.')
                                            : number_format($oferta->quantidade, 2, ',', '.') }}
                                    @endif

                                    {{ $oferta->unidade }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400">
                                    Propostas
                                </p>
                                <p class="font-semibold text-gray-800 mt-0.5">
                                    {{ $propostas->count() }}
                                </p>
                            </div>
                        </div>

                        {{-- MELHOR PROPOSTA --}}
                        @if($melhorValor !== null)
                            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 mb-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="material-symbols-outlined text-[#236350] text-lg">
                                        trending_up
                                    </span>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-[#236350]">
                                        Melhor valor por unidade
                                    </span>
                                </div>

                                <p class="text-2xl font-bold text-[#236350]">
                                    R$ {{ number_format($melhorValor, 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    por {{ $oferta->unidade }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 mb-4">
                                <p class="text-sm text-gray-500">
                                    Ainda não existem propostas para esta oferta.
                                </p>
                            </div>
                        @endif

                        {{-- QUANTIDADE NEGOCIADA --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-gray-500">
                                    Quantidade aceita
                                </span>
                                <span class="text-xs font-semibold text-gray-700">
                                    @if(strtolower($oferta->unidade) === 'saca')
                                        {{ number_format($quantidadeAceita, 0, ',', '.') }}
                                    @else
                                        {{ number_format($quantidadeAceita, 2, ',', '.') }}
                                    @endif
                                    /
                                    {{ number_format($oferta->quantidade, 0, ',', '.') }}
                                    {{ $oferta->unidade }}
                                </span>
                            </div>

                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#236350] rounded-full transition-all"
                                    style="width: {{ $percentualAceito }}%">
                                </div>
                            </div>
                        </div>

                        {{-- MAIOR VALOR TOTAL --}}
                        @if($maiorValorTotal !== null)
                            <div class="flex items-center justify-between text-sm mb-5">
                                <span class="text-gray-500">
                                    Maior proposta total
                                </span>
                                <span class="font-bold text-gray-800">
                                    R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        {{-- BOTÃO --}}
                        <a
                            href="{{ route('ofertas.propostas', $oferta) }}"
                            class="flex items-center justify-center gap-2 w-full bg-[#236350] hover:bg-[#1B4D3E] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-lg">
                                format_list_bulleted
                            </span>
                            Ver propostas
                            <span class="material-symbols-outlined text-base">
                                arrow_forward
                            </span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else

        {{-- ESTADO VAZIO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 text-3xl">
                    forum
                </span>
            </div>
            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhuma negociação encontrada
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Quando suas ofertas receberem negociações,
                elas aparecerão aqui.
            </p>
        </div>
    @endif

</div>

@endsection