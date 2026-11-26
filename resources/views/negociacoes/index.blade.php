@extends('layouts.layout')

@section('title', 'Minhas vendas')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
            >
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto my-6 px-4 flex flex-col items-start text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8 w-full text-left">
        <div class="text-left w-full">
            <p class="text-sm font-medium text-[#236350] dark:text-emerald-400 mb-1 text-left">
                Painel de negociações
            </p>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 text-left">
                Minhas ofertas
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-left">
                Acompanhe as propostas recebidas em suas ofertas.
            </p>
        </div>
    </div>

    {{-- Mensagens --}}
    @if(session('sucesso'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-green-200 dark:border-emerald-800/60 bg-green-50 dark:bg-emerald-950/50 px-4 py-3 text-sm text-green-800 dark:text-emerald-200">
            <span class="material-symbols-outlined text-lg leading-none">
                check_circle
            </span>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if(session('erro'))
        <div class="mb-5 w-full flex items-center gap-3 rounded-lg border border-red-200 dark:border-red-800/60 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-sm text-red-800 dark:text-red-200">
            <span class="material-symbols-outlined text-lg leading-none">
                error
            </span>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    @if($ofertas->count())

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 w-full text-left">

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

                {{-- Card da oferta --}}
                <div class="group bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-left">

                    {{-- Imagem --}}
                    <div class="relative h-44 bg-gray-100 dark:bg-slate-800 overflow-hidden">
                        <img
                            src="{{ $oferta->produto && $oferta->produto->imagem
                                ? (str_contains($oferta->produto->imagem, 'assets/')
                                    ? asset($oferta->produto->imagem)
                                    : asset('storage/' . $oferta->produto->imagem))
                                : asset('assets/milho.png') }}"
                            alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        {{-- Número da Ofertas --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/95 dark:bg-[#111827]/95 backdrop-blur-sm text-xs font-semibold text-gray-700 dark:text-gray-200 border border-transparent dark:border-[#273449] shadow-sm">
                                Oferta {{ $oferta->id }}
                            </span>
                        </div>

                        {{-- Número de Negociações --}}
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 dark:bg-[#111827]/95 text-gray-800 dark:text-gray-100 border border-transparent dark:border-[#273449] text-xs font-medium shadow-sm">
                                <span class="material-symbols-outlined text-sm leading-none">
                                    forum
                                </span>
                                <span class="leading-none">{{ $negociacoes->count() }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="p-5 text-left">

                        {{-- Produto --}}
                        <div class="mb-4 text-left">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-left">
                                <a
                                    href="{{ route('ofertas.show', $oferta) }}"
                                    class="hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
                                    title="Visitar oferta"
                                >
                                    {{ $oferta->produto->nome }}
                                </a>
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 text-left">
                                {{ $oferta->fornecedor->nome }}
                            </p>
                        </div>

                        {{-- Quantidade --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-left">
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400 dark:text-gray-500">
                                    Oferta
                                </p>
                                <p class="font-semibold text-gray-800 dark:text-gray-100 mt-0.5">
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
                                <p class="text-xs uppercase tracking-wide font-medium text-gray-400 dark:text-gray-500">
                                    Propostas
                                </p>
                                <p class="font-semibold text-gray-800 dark:text-gray-100 mt-0.5">
                                    {{ $propostas->count() }}
                                </p>
                            </div>
                        </div>

                        {{-- Melhor Proposta --}}
                        @if($melhorValor !== null)
                            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-800/60 p-4 mb-4 text-left">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                                        trending_up
                                    </span>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-[#236350] dark:text-emerald-300">
                                        Melhor valor por unidade
                                    </span>
                                </div>

                                <p class="text-2xl font-bold text-[#236350] dark:text-emerald-400 text-left">
                                    R$ {{ number_format($melhorValor, 2, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 text-left">
                                    por {{ $oferta->unidade }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4 mb-4 text-left">
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-left">
                                    Ainda não existem propostas para esta oferta.
                                </p>
                            </div>
                        @endif

                        {{-- Quantidade Negociada --}}
                        <div class="mb-4 text-left">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Quantidade aceita
                                </span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
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

                            <div class="w-full h-2 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-[#236350] dark:bg-emerald-500 rounded-full transition-all"
                                    style="width: {{ $percentualAceito }}%">
                                </div>
                            </div>
                        </div>

                        {{-- Maior valor total --}}
                        @if($maiorValorTotal !== null)
                            <div class="flex items-center justify-between text-sm mb-5">
                                <span class="text-gray-500 dark:text-gray-400">
                                    Maior proposta total
                                </span>
                                <span class="font-bold text-gray-800 dark:text-gray-100">
                                    R$ {{ number_format($maiorValorTotal, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        {{-- Botão de acesso --}}
                        @if(
                            auth()->user()->user_type === 'admin' ||
                            (
                                auth()->user()->fornecedor &&
                                auth()->user()->fornecedor->id === $oferta->fornecedor_id
                            )
                        )
                            {{-- Administrador ou fornecedor dono da oferta --}}
                            <a
                                href="{{ route('negociacoes.propostas', $oferta) }}"
                                class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors">

                                <span class="material-symbols-outlined text-lg leading-none">
                                    format_list_bulleted
                                </span>

                                <span>Ver propostas</span>

                                <span class="material-symbols-outlined text-base leading-none">
                                    arrow_forward
                                </span>
                            </a>
                        @else
                            {{-- Cliente: acessa somente a própria negociação --}}
                            <a
                                href="{{ route('negociacoes.show', $negociacoes->first()) }}"
                                class="btn-copav flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors">

                                <span class="material-symbols-outlined text-lg leading-none">
                                    forum
                                </span>

                                <span>Ver negociação</span>

                                <span class="material-symbols-outlined text-base leading-none">
                                    arrow_forward
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else

        {{-- Estado Vazio --}}
        <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] p-12 text-center shadow-sm w-full">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-3xl leading-none">
                    forum
                </span>
            </div>
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-100 mt-4">
                Nenhuma negociação encontrada
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Quando suas ofertas receberem negociações, elas aparecerão aqui.
            </p>
        </div>
    @endif

</div>

@endsection