@extends('layouts.layout')

@section('title', 'Minhas vendas')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li>
            <a href="{{route('home')}}">
                Início
            </a>
        </li>

        {{-- Item 2: Pégina Atual --}}
        <li class="flex items-center space-x-2 hover:cursor-pointer">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto my-6 px-4 flex flex-col items-start text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 w-full text-left">
        <div class="text-left w-full">
            <p class="text-sm font-medium text-[#236350] mb-1 text-left">
                Painel do Fornecedor
            </p>
            <h1 class="text-3xl font-bold text-gray-800 text-left">
                Minhas Vendas
            </h1>
            <p class="text-gray-500 mt-1 text-left">
                Acompanhe as propostas e negociações recebidas nos seus lotes anunciados.
            </p>
        </div>
    </div>

    {{-- Mensagens de Sucesso/Erro --}}
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

    @if(isset($ofertas) && $ofertas->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 w-full text-left">

            @foreach($ofertas as $oferta)

                @php
                    // Coleta todas as propostas feitas em todas as negociações desta oferta
                    $negociacoes = $oferta->negociacoes;

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

                {{-- Card da Oferta (Venda) --}}
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

                        {{-- Total de Negociações/Compradores Interessados --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#236350] text-white text-xs font-medium shadow-sm">
                                <span class="material-symbols-outlined text-sm">forum</span>
                                {{ $negociacoes->count() }} {{ $negociacoes->count() === 1 ? 'negociação' : 'negociações' }}
                            </span>
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="p-5 text-left">

                        {{-- Produto --}}
                        <div class="mb-4 text-left">
                            <h2 class="text-xl font-bold text-gray-800 text-left">
                                {{ $oferta->produto->nome ?? 'Produto' }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1 text-left">
                                Lote #{{ $oferta->id }}
                            </p>
                        </div>

                        {{-- Quantidade Oferecida e Propostas --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-left">
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400">
                                    Quantidade Total
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
                                    Total Propostas
                                </p>
                                <p class="font-semibold text-gray-800 mt-0.5">
                                    {{ $propostas->count() }}
                                </p>
                            </div>
                        </div>

                        {{-- Melhor Proposta por Unidade / Alerta quando não possui propostas --}}
                        @if($melhorValor !== null)
                            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 mb-4 text-left">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="material-symbols-outlined text-[#236350] text-lg">trending_up</span>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-[#236350]">
                                        Melhor valor por unidade
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-[#236350] text-left">
                                    R$ {{ number_format($melhorValor, 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5 text-left">
                                    por {{ $oferta->unidade }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-4 text-left flex items-start gap-3">
                                <span class="material-symbols-outlined text-amber-600 text-xl shrink-0 mt-0.5">info</span>
                                <div>
                                    <p class="text-xs font-bold text-amber-800 uppercase tracking-wide">
                                        Sem propostas ativas
                                    </p>
                                    <p class="text-xs text-amber-700 mt-0.5 leading-relaxed">
                                        Este lote ainda não recebeu propostas de compradores.
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Quantidade Aceita/Vendida --}}
                        <div class="mb-4 text-left">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-gray-500">
                                    Quantidade vendida
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

                        {{-- Maior valor total recebido --}}
                        @if($maiorValorTotal !== null)
                            <div class="flex items-center justify-between text-sm mb-5">
                                <span class="text-gray-500">Maior proposta total</span>
                                <span class="font-bold text-gray-800">
                                    R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        {{-- Botão: Gerenciar/Ver Propostas --}}
                        <a
                            href="{{ route('negociacoes.propostas', $oferta) }}"
                            class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-lg">format_list_bulleted</span>
                            Gerenciar propostas
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>

                    </div>
                </div>
            @endforeach
        </div>

    @else

        {{-- Estado Vazio Geral --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm w-full max-w-2xl mx-auto my-8">
            <div class="w-16 h-16 mx-auto rounded-full bg-amber-50 border border-amber-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-3xl">
                    storefront
                </span>
            </div>
            
            <h2 class="text-xl font-bold text-gray-800 mt-4">
                Nenhuma oferta ou venda cadastrada
            </h2>
            
            <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto leading-relaxed">
                Você ainda não cadastrou nenhum lote de produtos para venda no marketplace.
            </p>

            <div class="mt-6">
                <a href="{{ route('ofertas.create') }}" class="btn-copav inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Cadastrar nova oferta
                </a>
            </div>
        </div>

    @endif

</div>

@endsection