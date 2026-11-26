@extends('layouts.layout')

@section('title', 'Detalhes da Demanda')

@section('conteudo')

@php
    $usuarioLogado = auth()->user();
    $isDonoDemanda = $usuarioLogado !== null && (int) $usuarioLogado->id === (int) ($demanda->cliente_id ?? 0);

    $statusAtual = strtolower((string) ($demanda->status ?? 'aberta'));
    $statusClasses = match($statusAtual) {
        'aberta', 'publicada', 'ativa' =>
            'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',

        'encerrada', 'finalizada' =>
            'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-slate-700',

        'cancelada' =>
            'bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800/60',

        default =>
            'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
    };
    $demandaAberta = in_array($statusAtual, ['aberta', 'publicada', 'ativa'], true);
    $podeFazerOferta = $demandaAberta && ($usuarioLogado?->isFornecedor() ?? false) && ! $isDonoDemanda;

    $ofertasRecebidas = $demanda->ofertasDiretas ?? collect();
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-4 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        {{-- Item 2: Demandas --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a href="{{ route('demandas.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
                Demandas
            </a>
        </li>

        {{-- Item 3: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-6xl mx-auto px-4 py-6 text-left">

    {{-- CARD PRINCIPAL DA DEMANDA --}}
    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="p-6 border-b border-gray-100 dark:border-[#273449]">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none">
                            shopping_cart
                        </span>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Demanda
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
                        {{ $demanda->nome_produto ?? 'Produto não informado' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $demanda->categoria?->nome ?? 'Sem categoria' }}
                    </p>
                </div>

                {{-- Status --}}
                <div class="shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-sm font-semibold {{ $statusClasses }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        {{ ucfirst((string) ($demanda->status ?? 'aberta')) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- INFORMAÇÕES --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Empresa --}}
                <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-lg border border-gray-200/80 dark:border-[#273449]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                            business
                        </span>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Empresa
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $demanda->cliente?->nome ?? 'Não informado' }}
                    </p>
                </div>

                {{-- Quantidade --}}
                <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-lg border border-gray-200/80 dark:border-[#273449]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                            inventory_2
                        </span>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Quantidade
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $demanda->quantidade ?? 0 }}
                        {{ $demanda->unidade ?? '' }}
                    </p>
                </div>

                {{-- Valor máximo --}}
                <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-lg border border-gray-200/80 dark:border-[#273449]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                            payments
                        </span>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Valor máximo
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                        @if(!is_null($demanda->valor_maximo))
                            R$ {{ number_format((float) $demanda->valor_maximo, 2, ',', '.') }}
                        @else
                            Não informado
                        @endif
                    </p>
                </div>

                {{-- Localização --}}
                <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-lg border border-gray-200/80 dark:border-[#273449]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                            location_on
                        </span>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Localização
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $demanda->localizacao ?? 'Não informada' }}
                    </p>
                </div>
            </div>

            {{-- Descrição --}}
            <div class="mt-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">
                    Descrição
                </h2>
                <div class="p-4 bg-gray-50 dark:bg-[#161f30] rounded-lg border border-gray-200/80 dark:border-[#273449]">
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ $demanda->descricao ?? 'Nenhuma descrição foi informada.' }}
                    </p>
                </div>
            </div>

            {{-- Data limite --}}
            <div class="mt-5 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-lg leading-none">
                    event
                </span>
                <span>
                    <strong class="text-gray-700 dark:text-gray-100">Data limite:</strong>
                    @if($demanda->data_limite)
                        {{ \Carbon\Carbon::parse($demanda->data_limite)->format('d/m/Y') }}
                    @else
                        Não informada
                    @endif
                </span>
            </div>
        </div>

        {{-- Ação --}}
        @if($podeFazerOferta)
            <div class="px-6 py-4 bg-gray-50 dark:bg-[#161f30] border-t border-gray-100 dark:border-[#273449]">
                <a
                    href="{{ route('ofertas-diretas.create', ['demanda_id' => $demanda->id]) }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5
                           btn-copav text-white rounded-lg font-semibold text-sm
                           shadow-sm hover:opacity-95 transition-all">
                    <span class="material-symbols-outlined text-lg leading-none">
                        add_circle
                    </span>
                    <span>Fazer oferta para esta demanda</span>
                </a>
            </div>
        @endif
    </div>

    {{-- OFERTAS DOS FORNECEDORES --}}
    <div class="mt-8">

        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                    Ofertas dos fornecedores
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Confira as propostas recebidas para esta demanda.
                </p>
            </div>
            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-[#273449] px-3 py-1 text-xs font-semibold shrink-0">
                {{ $ofertasRecebidas->count() }}
                {{ $ofertasRecebidas->count() === 1 ? 'oferta' : 'ofertas' }}
            </span>
        </div>

        @if($ofertasRecebidas->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($ofertasRecebidas as $oferta)
                    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-sm border border-gray-200 dark:border-[#273449] hover:shadow-md transition-shadow overflow-hidden">

                        {{-- Cabeçalho da oferta --}}
                        <div class="p-5 border-b border-gray-100 dark:border-[#273449]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/60
                                                flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none">
                                            local_shipping
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Fornecedor
                                        </p>
                                        <h3 class="font-bold text-gray-800 dark:text-gray-100">
                                            {{ $oferta->fornecedor?->nome ?? 'Fornecedor não informado' }}
                                        </h3>
                                    </div>
                                </div>

                                {{-- Status da oferta --}}
                                @php
                                    $statusOferta = match(strtolower((string) ($oferta->status ?? ''))) {
                                        'aceita', 'aprovada' =>
                                            'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                                        'recusada', 'cancelada' =>
                                            'bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800/60',
                                        default =>
                                            'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full border text-xs font-semibold shrink-0 {{ $statusOferta }}">
                                    {{ ucfirst((string) ($oferta->status ?? 'pendente')) }}
                                </span>
                            </div>
                        </div>

                        {{-- Dados da oferta --}}
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        Quantidade
                                    </p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $oferta->quantidade ?? 0 }}
                                        {{ $demanda->unidade ?? '' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        Valor
                                    </p>
                                    <p class="font-bold text-[#236350] dark:text-emerald-400 text-lg">
                                        R$ {{ number_format((float) ($oferta->valor ?? 0), 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    Observação
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $oferta->observacao ?? 'Sem observação.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-[#111827] border border-dashed border-gray-300 dark:border-[#273449] rounded-xl p-10 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500 leading-none">
                    inbox
                </span>
                <h3 class="mt-3 font-semibold text-gray-700 dark:text-gray-200">
                    Nenhuma oferta ainda
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ainda não há fornecedores que fizeram uma oferta para esta demanda.
                </p>
            </div>
        @endif
    </div>
</div>

@endsection