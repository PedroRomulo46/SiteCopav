@extends('layouts.layout')

@section('title', 'Ofertas Diretas')

@section('conteudo')

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
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 my-6 text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100">
                Ofertas Diretas
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Acompanhe as ofertas diretas enviadas para demandas abertas.
            </p>
        </div>

        <a
            href="{{ route('ofertas-diretas.create') }}"
            class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm shrink-0 transition-opacity hover:opacity-95"
        >
            <span class="material-symbols-outlined text-lg leading-none">add_circle</span>
            <span>Fazer nova oferta</span>
        </a>
    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 leading-none shrink-0">check_circle</span>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if($ofertasDiretas->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($ofertasDiretas as $oferta)
                @php
                    $statusAtual = strtolower((string) ($oferta->status ?? 'pendente'));

                    $statusClasses = [
                        'pendente' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                        'aceita' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                        'recusada' => 'bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800/60',
                        'cancelada' => 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700',
                    ];

                    $badgeClass = $statusClasses[$statusAtual] ?? 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-slate-700';
                    $nomeProduto = $oferta->demanda?->nome_produto ?? 'Produto não informado';
                    $nomeEmpresa = $oferta->demanda?->cliente?->nome
                        ?? $oferta->demanda?->cliente?->name
                        ?? 'Empresa não informada';
                    $nomeFornecedor = $oferta->fornecedor?->nome
                        ?? $oferta->fornecedor?->name
                        ?? 'Fornecedor não informado';
                    $unidade = $oferta->demanda?->unidade ?? '';
                @endphp

                <article class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm hover:shadow-md transition-all p-5 flex flex-col">
                    <div class="flex items-start justify-between gap-3 border-b border-gray-100 dark:border-[#273449] pb-3 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                {{ $nomeProduto }}
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Fornecedor: <strong class="text-gray-700 dark:text-gray-200">{{ $nomeFornecedor }}</strong>
                            </p>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border capitalize shrink-0 {{ $badgeClass }}">
                            {{ str_replace('_', ' ', $statusAtual) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mb-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400">Empresa:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">{{ $nomeEmpresa }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400">Quantidade oferecida:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $oferta->quantidade }} {{ $unidade }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100 dark:border-[#273449]">
                            <span class="text-gray-500 dark:text-gray-400">Valor:</span>
                            <span class="text-lg font-bold text-[#236350] dark:text-emerald-400">
                                R$ {{ number_format((float) ($oferta->valor ?? 0), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a
                            href="{{ route('ofertas-diretas.show', $oferta) }}"
                            class="btn-copav w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-95"
                        >
                            <span class="material-symbols-outlined text-lg leading-none">visibility</span>
                            <span>Ver oferta</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] p-10 text-center shadow-sm">
            <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600 leading-none">local_offer</span>
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-100 mt-3">
                Nenhuma oferta direta cadastrada
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Ainda não há ofertas diretas registradas no sistema.
            </p>
        </div>
    @endif

</div>

@endsection