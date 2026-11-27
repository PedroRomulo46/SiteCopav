@extends('layouts.layout')

@section('title', 'Painel Administrativo')

@section('conteudo')

{{-- Breadcrumbs --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
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

<div class="w-full max-w-7xl mx-auto space-y-6 text-left">

    {{-- Cabeçalho do Painel + Botão para o Mapa --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-[#111827] p-6 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-[#236350] dark:text-emerald-400 mb-1">
                <span class="material-symbols-outlined text-base leading-none">shield_person</span>
                <span>Área Administrativa</span>
            </span>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Painel de Administração
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Gerencie os recursos da plataforma e visualize a distribuição geográfica dos fornecedores.
            </p>
        </div>

        <a
            href="{{ route('admin.fornecedores.mapa') }}"
            class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-95 shrink-0"
        >
            <span class="material-symbols-outlined text-xl leading-none">map</span>
            <span>Ver Mapa de Fornecedores</span>
        </a>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuários</span>
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400">group</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalUsuarios ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fornecedores</span>
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400">storefront</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalFornecedores ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ofertas</span>
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400">inventory_2</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalOfertas ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Demandas</span>
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400">assignment</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalDemandas ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-[#111827] p-5 rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Negociações</span>
                <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400">handshake</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalNegociacoes ?? 0 }}</p>
        </div>
    </div>

    {{-- Atalhos Administrativos --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <a
            href="{{ route('admin.fornecedores.mapa') }}"
            class="group bg-white dark:bg-[#111827] p-6 rounded-2xl border border-gray-200 dark:border-[#273449] hover:border-[#236350] dark:hover:border-emerald-500 shadow-sm transition-all flex flex-col justify-between gap-4"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl text-[#236350] dark:text-emerald-400">location_on</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 group-hover:text-[#236350] dark:group-hover:text-emerald-400 transition-colors">
                        Mapa de Fornecedores
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Visualize no OpenStreetMap todos os fornecedores cadastrados por cidade e região.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end text-xs font-semibold text-[#236350] dark:text-emerald-400">
                <span>Abrir mapa</span>
                <span class="material-symbols-outlined text-base ml-1">arrow_forward</span>
            </div>
        </a>

        <a
            href="{{ route('ofertas.index') }}"
            class="group bg-white dark:bg-[#111827] p-6 rounded-2xl border border-gray-200 dark:border-[#273449] hover:border-[#236350] dark:hover:border-emerald-500 shadow-sm transition-all flex flex-col justify-between gap-4"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl text-[#236350] dark:text-emerald-400">inventory_2</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 group-hover:text-[#236350] dark:group-hover:text-emerald-400 transition-colors">
                        Gerenciar Ofertas
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Acompanhe os lotes publicados, estoques e valores disponíveis no marketplace.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end text-xs font-semibold text-[#236350] dark:text-emerald-400">
                <span>Ver ofertas</span>
                <span class="material-symbols-outlined text-base ml-1">arrow_forward</span>
            </div>
        </a>

        <a
            href="{{ route('demandas.index') }}"
            class="group bg-white dark:bg-[#111827] p-6 rounded-2xl border border-gray-200 dark:border-[#273449] hover:border-[#236350] dark:hover:border-emerald-500 shadow-sm transition-all flex flex-col justify-between gap-4"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/60 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl text-[#236350] dark:text-emerald-400">assignment</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 group-hover:text-[#236350] dark:group-hover:text-emerald-400 transition-colors">
                        Gerenciar Demandas
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Consulte as demandas registradas pelas empresas e acompanhe as negociações.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end text-xs font-semibold text-[#236350] dark:text-emerald-400">
                <span>Ver demandas</span>
                <span class="material-symbols-outlined text-base ml-1">arrow_forward</span>
            </div>
        </a>
    </div>

</div>
@endsection