@extends('layouts.layout')
@section('title', 'Menu Fornecedor')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
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

<div class="w-full max-w-4xl mx-auto my-6 px-4">

    {{-- Card Principal --}}
    <div class="w-full bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-6 sm:p-8 overflow-hidden">

        {{-- Cabeçalho Centralizado --}}
        <div class="flex flex-col items-center justify-center text-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-[#273449] w-full">

            {{-- Título --}}
            <div class="w-full text-center">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight">{{ $fornecedor->nome }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Detalhes completos do fornecedor cadastrado</p>
            </div>

            {{-- Botões no Meio --}}
            @php
                $usuarioAtual = auth()->user();
                $podeGerenciarFornecedor = $usuarioAtual !== null
                    && ((int) $usuarioAtual->id === (int) $fornecedor->user_id || $usuarioAtual->isAdmin());
            @endphp

            @if($podeGerenciarFornecedor)
                <div class="flex items-center justify-center gap-3 shrink-0">
                    <a
                        href="{{ route('fornecedores.edit', $fornecedor) }}"
                        class="inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm"
                    >
                        <span class="material-symbols-outlined text-base leading-none">edit</span>
                        <span>Editar</span>
                    </a>

                    <form action="{{ route('fornecedores.destroy', $fornecedor) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-base leading-none">delete</span>
                            <span>Excluir</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>

        {{-- Grid de Informações --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Documento</span>
                <p class="mt-1 text-sm sm:text-base text-gray-900 dark:text-gray-100 font-semibold">{{ $fornecedor->documento }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Telefone</span>
                <p class="mt-1 text-sm sm:text-base text-gray-900 dark:text-gray-100 font-semibold">{{ $fornecedor->telefone }}</p>
            </div>

            <div class="md:col-span-2">
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Descrição</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800 dark:text-gray-200">{{ $fornecedor->descricao ?? 'Não informada' }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Endereço</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800 dark:text-gray-200">{{ $fornecedor->endereco }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Cidade / Estado</span>
                <p class="mt-1 text-sm sm:text-base text-gray-800 dark:text-gray-200">{{ $fornecedor->cidade }} - {{ $fornecedor->estado }}</p>
            </div>

            <div>
                <span class="block text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Status</span>
                <p class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $fornecedor->status === 'ativo' ? 'bg-green-100 dark:bg-emerald-950/60 text-green-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60' : 'bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-gray-300 border border-transparent dark:border-slate-700' }}">
                        {{ ucfirst($fornecedor->status) }}
                    </span>
                </p>
            </div>
        </div>

    </div>

</div>

@endsection