@extends('layouts.layout')

@section('title', 'Detalhes da Oferta Direta')

@section('conteudo')

@php
    $demanda = $ofertaDireta->demanda ?? null;
    $nomeProduto = $demanda?->nome_produto ?? 'Produto não informado';
    $nomeEmpresa = $demanda?->cliente?->nome
        ?? $demanda?->cliente?->name
        ?? 'Empresa não informada';
    $nomeCategoria = $demanda?->categoria?->nome ?? 'Não informada';
    $unidadeDemanda = $demanda?->unidade ?? '';
    $nomeFornecedor = $ofertaDireta->fornecedor?->nome
        ?? $ofertaDireta->fornecedor?->name
        ?? 'Fornecedor não informado';

    $statusAtual = strtolower((string) ($ofertaDireta->status ?? 'pendente'));
    $statusClasses = [
        'pendente' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
        'aceita' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
        'recusada' => 'bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800/60',
        'cancelada' => 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700',
    ];
    $badgeClass = $statusClasses[$statusAtual] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700';
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a href="{{ route('ofertas-diretas.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Ofertas Diretas
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto px-2 sm:px-4 my-6 space-y-6 text-left">

    {{-- Card Principal --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm p-6">

        {{-- Cabeçalho --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 border-b border-gray-100 dark:border-[#273449] pb-5 mb-6">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-[#236350] dark:text-emerald-400 block mb-1">
                    Detalhes da Oferta Direta
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $nomeProduto }}
                </h1>
            </div>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border capitalize w-fit {{ $badgeClass }}">
                {{ str_replace('_', ' ', $statusAtual) }}
            </span>
        </div>

        {{-- Seção: Demanda da empresa --}}
        <section class="mb-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none">business</span>
                <span>Demanda da empresa</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">{{ $nomeEmpresa }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Categoria</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">{{ $nomeCategoria }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantidade solicitada</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">
                        {{ $demanda?->quantidade ?? '—' }} {{ $unidadeDemanda }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor máximo</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">
                        @if(!is_null($demanda?->valor_maximo))
                            R$ {{ number_format((float) $demanda->valor_maximo, 2, ',', '.') }}
                        @else
                            Não informado
                        @endif
                    </span>
                </div>
            </div>
        </section>

        {{-- Seção: Oferta do fornecedor --}}
        <section class="pt-5 border-t border-gray-100 dark:border-[#273449]">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none">local_offer</span>
                <span>Oferta do fornecedor</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fornecedor</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">{{ $nomeFornecedor }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantidade oferecida</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block">
                        {{ $ofertaDireta->quantidade }} {{ $unidadeDemanda }}
                    </span>
                </div>

                <div class="p-3.5 bg-emerald-50/70 dark:bg-emerald-950/50 rounded-xl border border-emerald-100 dark:border-emerald-800/60">
                    <span class="block text-xs font-medium text-emerald-800 dark:text-emerald-300 uppercase">Valor</span>
                    <span class="text-lg font-bold text-[#236350] dark:text-emerald-400 mt-0.5 block">
                        R$ {{ number_format((float) ($ofertaDireta->valor ?? 0), 2, ',', '.') }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449]">
                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</span>
                    <span class="text-base font-semibold text-gray-800 dark:text-gray-100 mt-0.5 block capitalize">
                        {{ str_replace('_', ' ', $statusAtual) }}
                    </span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-xl border border-gray-200/80 dark:border-[#273449] text-sm">
                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Observação</span>
                <p class="text-gray-700 dark:text-gray-200 whitespace-pre-line">
                    {{ $ofertaDireta->observacao ?? 'Sem observação' }}
                </p>
            </div>
        </section>

        {{-- Ações --}}
        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-[#273449] flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('ofertas-diretas.index') }}"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors"
            >
                <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
                <span>Voltar</span>
            </a>

            @if($demanda)
                <a
                    href="{{ route('demandas.show', $demanda) }}"
                    class="btn-copav inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-95"
                >
                    <span class="material-symbols-outlined text-lg leading-none">assignment</span>
                    <span>Voltar para a demanda</span>
                </a>
            @endif
        </div>

    </div>

</div>

@endsection