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
        'pendente' => 'bg-amber-100 text-amber-800 border-amber-200',
        'aceita' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'recusada' => 'bg-red-100 text-red-800 border-red-200',
        'cancelada' => 'bg-gray-100 text-gray-700 border-gray-200',
    ];
    $badgeClass = $statusClasses[$statusAtual] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                Início
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{ route('ofertas-diretas.index') }}" class="hover:text-[#1B4D3E]">
                Ofertas Diretas
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto px-2 sm:px-4 my-6 space-y-6 text-left">

    {{-- Card Principal --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        {{-- Cabeçalho --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 border-b border-gray-100 pb-5 mb-6">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-[#236350] block mb-1">
                    Detalhes da Oferta Direta
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    {{ $nomeProduto }}
                </h1>
            </div>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border capitalize w-fit {{ $badgeClass }}">
                {{ str_replace('_', ' ', $statusAtual) }}
            </span>
        </div>

        {{-- Seção: Demanda da empresa --}}
        <section class="mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#236350] text-xl">business</span>
                Demanda da empresa
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Empresa</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">{{ $nomeEmpresa }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Categoria</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">{{ $nomeCategoria }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Quantidade solicitada</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">
                        {{ $demanda?->quantidade ?? '—' }} {{ $unidadeDemanda }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Valor máximo</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">
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
        <section class="pt-5 border-t border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#236350] text-xl">local_offer</span>
                Oferta do fornecedor
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Fornecedor</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">{{ $nomeFornecedor }}</span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Quantidade oferecida</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block">
                        {{ $ofertaDireta->quantidade }} {{ $unidadeDemanda }}
                    </span>
                </div>

                <div class="p-3.5 bg-emerald-50/70 rounded-xl border border-emerald-100">
                    <span class="block text-xs font-medium text-emerald-800 uppercase">Valor</span>
                    <span class="text-lg font-bold text-[#236350] mt-0.5 block">
                        R$ {{ number_format((float) ($ofertaDireta->valor ?? 0), 2, ',', '.') }}
                    </span>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="block text-xs font-medium text-gray-500 uppercase">Status</span>
                    <span class="text-base font-semibold text-gray-800 mt-0.5 block capitalize">
                        {{ str_replace('_', ' ', $statusAtual) }}
                    </span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-sm">
                <span class="block text-xs font-medium text-gray-500 uppercase mb-1">Observação</span>
                <p class="text-gray-700 whitespace-pre-line">
                    {{ $ofertaDireta->observacao ?? 'Sem observação' }}
                </p>
            </div>
        </section>

        {{-- Ações --}}
        <div class="mt-6 pt-5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('ofertas-diretas.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors"
            >
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Voltar
            </a>

            @if($demanda)
                <a
                    href="{{ route('demandas.show', $demanda) }}"
                    class="btn-copav inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-90"
                >
                    <span class="material-symbols-outlined text-lg">assignment</span>
                    Voltar para a demanda
                </a>
            @endif
        </div>

    </div>

</div>

@endsection