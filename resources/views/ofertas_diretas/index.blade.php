@extends('layouts.layout')

@section('title', 'Ofertas Diretas')

@section('conteudo')

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
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 my-6 text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Ofertas Diretas
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Acompanhe as ofertas diretas enviadas para demandas abertas.
            </p>
        </div>

        <a
            href="{{ route('ofertas-diretas.create') }}"
            class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-md text-sm font-semibold shadow-sm shrink-0 transition-opacity hover:opacity-90"
        >
            <span class="material-symbols-outlined text-lg">add_circle</span>
            Fazer nova oferta
        </a>
    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if($ofertasDiretas->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($ofertasDiretas as $oferta)
                @php
                    $statusAtual = strtolower((string) ($oferta->status ?? 'pendente'));

                    $statusClasses = [
                        'pendente' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'aceita' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'recusada' => 'bg-red-100 text-red-800 border-red-200',
                        'cancelada' => 'bg-gray-100 text-gray-700 border-gray-200',
                    ];

                    $badgeClass = $statusClasses[$statusAtual] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                    $nomeProduto = $oferta->demanda?->nome_produto ?? 'Produto não informado';
                    $nomeEmpresa = $oferta->demanda?->cliente?->nome
                        ?? $oferta->demanda?->cliente?->name
                        ?? 'Empresa não informada';
                    $nomeFornecedor = $oferta->fornecedor?->nome
                        ?? $oferta->fornecedor?->name
                        ?? 'Fornecedor não informado';
                    $unidade = $oferta->demanda?->unidade ?? '';
                @endphp

                <article class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col">
                    <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">
                                {{ $nomeProduto }}
                            </h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Fornecedor: <strong class="text-gray-700">{{ $nomeFornecedor }}</strong>
                            </p>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border capitalize shrink-0 {{ $badgeClass }}">
                            {{ str_replace('_', ' ', $statusAtual) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 mb-5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-500">Empresa:</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $nomeEmpresa }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-gray-500">Quantidade oferecida:</span>
                            <span class="font-semibold text-gray-800">
                                {{ $oferta->quantidade }} {{ $unidade }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                            <span class="text-gray-500">Valor:</span>
                            <span class="text-lg font-bold text-[#236350]">
                                R$ {{ number_format((float) ($oferta->valor ?? 0), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a
                            href="{{ route('ofertas-diretas.show', $oferta) }}"
                            class="btn-copav w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-90"
                        >
                            <span class="material-symbols-outlined text-lg">visibility</span>
                            Ver oferta
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center shadow-sm">
            <span class="material-symbols-outlined text-5xl text-gray-300">local_offer</span>
            <h2 class="text-xl font-bold text-gray-700 mt-3">
                Nenhuma oferta direta cadastrada
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Ainda não há ofertas diretas registradas no sistema.
            </p>
        </div>
    @endif

</div>

@endsection