@extends('layouts.layout')
@section('title', 'Demandas')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-4 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
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

<div class="w-full max-w-7xl mx-auto px-6 py-6 sm:px-4 text-left space-y-6">
    {{-- Cabeçalho da Página --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200 dark:border-[#273449]">
        <div class="text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">assignment</span>
                <span>Minhas Demandas</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Acompanhe e gerencie as solicitações das demandas da empresa
            </p>
        </div>

        @if(auth()->user()?->podeGerenciarDemandas())
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a
                    href="{{ route('demandas.create') }}"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors whitespace-nowrap"
                >
                    <span class="material-symbols-outlined text-lg leading-none">add</span>
                    <span>Nova Demanda</span>
                </a>
            </div>
        @endif
    </div>

    {{-- Alerta de Sucesso --}}
    @if(session('sucesso'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md text-left flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-xl leading-none">check_circle</span>
                <span>{{ session('sucesso') }}</span>
            </div>
            <button
                type="button"
                onclick="this.parentElement.remove()"
                class="inline-flex h-6 w-6 items-center justify-center rounded-full text-emerald-800 dark:text-emerald-200 hover:bg-emerald-100/70 dark:hover:bg-emerald-900/40 cursor-pointer transition-colors"
                aria-label="Fechar alerta"
            >
                ✕
            </button>
        </div>
    @endif

    {{-- Grid de Demandas --}}
    @if($demandas->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($demandas as $demanda)
                <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">

                    {{-- Conteúdo do Card --}}
                    <div class="p-5 text-left">

                        {{-- Título e Status --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors line-clamp-1">
                                {{ $demanda->nome_produto }}
                            </h2>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-[#1B4D3E] dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 text-xs py-1 px-2.5 font-medium shrink-0">
                                {{ $demanda->status }}
                            </span>
                        </div>

                        {{-- Detalhes da Demanda --}}
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300 mt-4">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">business</span>
                                <span class="truncate"><strong class="text-gray-800 dark:text-gray-100">Empresa:</strong> {{ $demanda->cliente->nome ?? 'Não informada' }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">category</span>
                                <span class="truncate"><strong class="text-gray-800 dark:text-gray-100">Categoria:</strong> {{ $demanda->categoria->nome ?? 'Sem categoria' }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">inventory_2</span>
                                <span><strong class="text-gray-800 dark:text-gray-100">Qtd:</strong> {{ $demanda->quantidade }} {{ $demanda->unidade }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">payments</span>
                                <span><strong class="text-gray-800 dark:text-gray-100">Máx:</strong>
                                    @if($demanda->valor_maximo)
                                        R$ {{ number_format($demanda->valor_maximo, 2, ',', '.') }}
                                    @else
                                        Não informado
                                    @endif
                                </span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">location_on</span>
                                <span class="truncate"><strong class="text-gray-800 dark:text-gray-100">Local:</strong> {{ $demanda->localizacao ?? 'Não informada' }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">event</span>
                                <span><strong class="text-gray-800 dark:text-gray-100">Limite:</strong>
                                    @if($demanda->data_limite)
                                        {{ $demanda->data_limite->format('d/m/Y') }}
                                    @else
                                        Não informada
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Ações do Card --}}
                    <div class="px-5 py-3 bg-gray-50 dark:bg-[#161f30] border-t border-gray-100 dark:border-[#273449] flex items-center justify-between gap-2">
                        <a
                            href="{{ route('demandas.show', $demanda) }}"
                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-md text-xs font-medium text-[#1B4D3E] dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-[#143B2F] dark:hover:text-emerald-300 transition-colors w-full whitespace-nowrap"
                        >
                            <span class="material-symbols-outlined text-base leading-none">visibility</span>
                            <span>Ver demanda</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else

        {{-- Estado Vazio --}}
        <div class="text-center py-12 bg-white dark:bg-[#111827] rounded-xl border border-dashed border-gray-300 dark:border-[#273449] p-8 my-6">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 text-[#1B4D3E] dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl leading-none">assignment</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Nenhuma demanda cadastrada</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Ainda não existem solicitações de compra cadastradas no sistema.</p>

            @if(auth()->user()?->podeGerenciarDemandas())
                <a
                    href="{{ route('demandas.create') }}"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors"
                >
                    <span class="material-symbols-outlined text-xl leading-none">add</span>
                    <span>Cadastrar nova demanda</span>
                </a>
            @endif
        </div>
    @endif

</div>

@endsection