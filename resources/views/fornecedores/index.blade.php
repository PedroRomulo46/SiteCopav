@extends('layouts.layout')
@section('title', 'Fornecedores')

@section('conteudo')

{{-- Breadcrumbs --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li>
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
            >
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

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left space-y-6">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-[#273449] pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">storefront</span>
                <span>Lista de Fornecedores</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Consulte os fornecedores cadastrados no marketplace
            </p>
        </div>

        @auth
            @php
                $fornecedorUsuario = auth()->user()?->fornecedor;
            @endphp

            @if($fornecedorUsuario)
                <a
                    href="{{ route('fornecedores.show', $fornecedorUsuario) }}"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors shrink-0 whitespace-nowrap"
                >
                    <span class="material-symbols-outlined text-xl leading-none">store</span>
                    <span>Meu fornecedor</span>
                </a>
            @else
                <a
                    href="{{ route('fornecedores.create') }}"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors shrink-0 whitespace-nowrap"
                >
                    <span class="material-symbols-outlined text-xl leading-none">add</span>
                    <span>Cadastrar fornecedor</span>
                </a>
            @endif
        @endauth
    </div>

    {{-- Alerta de Sucesso --}}
    @if(session('sucesso'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md flex items-center justify-between shadow-sm">
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

    {{-- Grid de Fornecedores --}}
    @if($fornecedores->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($fornecedores as $fornecedor)
                <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 truncate">
                                {{ $fornecedor->nome }}
                            </h2>

                            @if($fornecedor->status)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-[#1B4D3E] dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 px-2.5 py-0.5 text-xs font-medium shrink-0">
                                    {{ ucfirst($fornecedor->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">badge</span>
                                <span class="truncate"><strong class="text-gray-800 dark:text-gray-100">Documento:</strong> {{ $fornecedor->documento }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">call</span>
                                <span class="truncate"><strong class="text-gray-800 dark:text-gray-100">Telefone:</strong> {{ $fornecedor->telefone }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">location_on</span>
                                <span class="truncate">
                                    <strong class="text-gray-800 dark:text-gray-100">Local:</strong> {{ $fornecedor->cidade }}{{ $fornecedor->estado ? ' - ' . $fornecedor->estado : '' }}
                                </span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400 dark:text-gray-500 leading-none shrink-0">person</span>
                                <span class="truncate">
                                    <strong class="text-gray-800 dark:text-gray-100">Usuário:</strong> {{ $fornecedor->usuario->nome ?? $fornecedor->usuario->name ?? 'Não informado' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-gray-50 dark:bg-[#161f30] border-t border-gray-100 dark:border-[#273449] flex items-center justify-end">
                        <a
                            href="{{ route('fornecedores.show', $fornecedor) }}"
                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-md text-xs font-medium text-[#1B4D3E] dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-[#143B2F] dark:hover:text-emerald-300 transition-colors whitespace-nowrap"
                        >
                            <span class="material-symbols-outlined text-base leading-none">visibility</span>
                            <span>Ver detalhes</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white dark:bg-[#111827] rounded-xl border border-dashed border-gray-300 dark:border-[#273449] p-8 my-6">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 text-[#1B4D3E] dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl leading-none">storefront</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Nenhum fornecedor cadastrado</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Ainda não existem fornecedores registrados no sistema.</p>
        </div>
    @endif

</div>

@endsection