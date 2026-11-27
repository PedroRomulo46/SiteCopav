@extends('layouts.layout')

@section('title', 'Categorias')

@section('conteudo')

@php
    $isAdmin = (bool) (auth()->user()?->isAdmin());
@endphp

<div
    x-data="{
        modalExcluir: false,
        categoriaNome: '',
        acaoExcluir: '',
        confirmarExclusao(nome, url) {
            this.categoriaNome = nome;
            this.acaoExcluir = url;
            this.modalExcluir = true;
        }
    }"
    @keydown.escape.window="modalExcluir = false"
>
    {{-- breadcrumbs com setas garantidas via ícone --}}
    <nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">

            {{-- item 1: início --}}
            <li>
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                >
                    Início
                </a>
            </li>

            {{-- item 2: página atual --}}
            <li class="inline-flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
                <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                    @yield('title')
                </span>
            </li>
        </ol>
    </nav>

    <div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

        {{-- alerta de sucesso --}}
        @if(session('sucesso'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md text-left flex items-center justify-between">
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

        {{-- cabeçalho da página --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
            <div class="text-left">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">category</span>
                    <span>Lista de Categorias</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $isAdmin
                        ? 'Gerencie as categorias de produtos cadastradas no sistema'
                        : 'Explore as categorias de produtos disponíveis no sistema' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @auth
                    <a
                        href="{{ route('categorias.sugerir') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border border-[#1B4D3E] dark:border-emerald-500/60 text-[#1B4D3E] dark:text-emerald-300 bg-transparent hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors whitespace-nowrap"
                    >
                        <span class="material-symbols-outlined text-lg leading-none">lightbulb</span>
                        <span>Sugerir Categoria</span>
                    </a>
                @endauth

                @if($isAdmin)
                    <a
                        href="{{ route('categorias.create') }}"
                        class="btn-copav px-4 py-2 rounded-lg text-sm font-medium gap-2 shadow-sm transition-colors whitespace-nowrap"
                    >
                        <span class="material-symbols-outlined text-lg leading-none">add</span>
                        <span>Cadastrar Categoria</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- grid de categorias --}}
        @if($categorias->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categorias as $categoria)
                    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">

                        {{-- conteúdo do card --}}
                        <div class="p-5 text-left">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                                    {{ $categoria->nome }}
                                </h2>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                                {{ $categoria->descricao ?? 'Sem descrição informada.' }}
                            </p>
                        </div>

                        {{-- ações do card --}}
                        <div class="px-5 py-3 bg-gray-50 dark:bg-[#161f30] border-t border-gray-100 dark:border-[#273449] flex items-center justify-between gap-2">

                            <a
                                href="{{ route('categorias.show', [$categoria, 'origem' => 'index']) }}"
                                class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-[#1B4D3E] dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-[#143B2F] dark:hover:text-emerald-300 transition-colors whitespace-nowrap {{ $isAdmin ? '' : 'w-full' }}"
                            >
                                <span class="material-symbols-outlined text-base leading-none">visibility</span>
                                <span>Ver</span>
                            </a>

                            @if($isAdmin)
                                <div class="flex items-center gap-1">
                                    <a
                                        href="{{ route('categorias.edit', $categoria) }}"
                                        class="inline-flex items-center justify-center gap-1 px-2 py-1 rounded-md text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition-colors whitespace-nowrap"
                                    >
                                        <span class="material-symbols-outlined text-base leading-none">edit</span>
                                        <span>Editar</span>
                                    </a>

                                    <button
                                        type="button"
                                        @click="confirmarExclusao(@js($categoria->nome), '{{ route('categorias.destroy', $categoria) }}')"
                                        class="inline-flex items-center justify-center gap-1 px-2 py-1 rounded-md text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-700 dark:hover:text-red-300 cursor-pointer transition-colors whitespace-nowrap"
                                    >
                                        <span class="material-symbols-outlined text-base leading-none">delete</span>
                                        <span>Excluir</span>
                                    </button>
                                </div>
                            @endif

                        </div>

                    </div>
                @endforeach
            </div>
        @else
            {{-- estado vazio --}}
            <div class="text-center py-12 bg-white dark:bg-[#111827] rounded-xl border border-dashed border-gray-300 dark:border-[#273449] p-8 my-6">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-950/50 text-[#1B4D3E] dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl leading-none">category</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Nenhuma categoria cadastrada</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    {{ $isAdmin
                        ? 'Comece adicionando a primeira categoria para organizar seus produtos.'
                        : 'Não há categorias cadastradas no sistema no momento.' }}
                </p>

                @if($isAdmin)
                    <a
                        href="{{ route('categorias.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-[#1B4D3E] hover:bg-[#143B2F] text-white transition-colors"
                    >
                        <span class="material-symbols-outlined text-lg leading-none">add</span>
                        <span>Cadastrar Categoria</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- seção de sugestões enviadas pelos usuários --}}
        @if($isAdmin)
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-[#273449]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-2xl leading-none">lightbulb</span>
                            <span>Sugestões de Categorias</span>
                            @if(isset($sugestoes) && $sugestoes->isNotEmpty())
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-500/30">
                                    {{ $sugestoes->count() }}
                                </span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Sugestões enviadas pelos usuários aguardando avaliação
                        </p>
                    </div>
                </div>

                @if(isset($sugestoes) && $sugestoes->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($sugestoes as $sugestao)
                            <div class="bg-white dark:bg-[#111827] rounded-xl border border-amber-200/80 dark:border-amber-500/30 shadow-sm flex flex-col justify-between overflow-hidden">
                                <div class="p-5 text-left">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $sugestao->nome }}
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40 shrink-0">
                                            Pendente
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-3">
                                        {{ $sugestao->descricao ?? 'Sem descrição informada.' }}
                                    </p>

                                    <div class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm leading-none">person</span>
                                        <span>{{ $sugestao->user->nome ?? 'Usuário' }}</span>
                                        <span>•</span>
                                        <span>{{ $sugestao->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                {{-- ações para aprovar ou recusar --}}
                                <div class="px-5 py-3 bg-amber-50/40 dark:bg-amber-950/20 border-t border-amber-100 dark:border-amber-500/20 flex items-center justify-between gap-2">
                                    <form action="{{ route('categorias.sugerir.aprovar', $sugestao) }}" method="POST" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-1 px-2 py-1 rounded-md text-xs font-medium text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-800 dark:hover:text-emerald-300 cursor-pointer transition-colors whitespace-nowrap"
                                        >
                                            <span class="material-symbols-outlined text-base leading-none">check</span>
                                            <span>Aprovar</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('categorias.sugerir.recusar', $sugestao) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-1 px-2 py-1 rounded-md text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-700 dark:hover:text-red-300 cursor-pointer transition-colors whitespace-nowrap"
                                        >
                                            <span class="material-symbols-outlined text-base leading-none">close</span>
                                            <span>Recusar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-[#111827] rounded-xl border border-dashed border-gray-200 dark:border-[#273449] p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nenhuma sugestão de categoria pendente no momento.
                    </div>
                @endif
            </div>
        @endif

    </div>

    {{-- modal de confirmação de exclusão (apenas admin) --}}
    @if($isAdmin)
        <div
            x-show="modalExcluir"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
            @click.self="modalExcluir = false"
        >
            <div
                x-show="modalExcluir"
                x-transition.scale.95.duration.150ms
                class="bg-white dark:bg-[#111827] rounded-2xl shadow-xl border border-gray-100 dark:border-[#273449] max-w-md w-full p-6 text-left"
            >
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl leading-none">warning</span>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Excluir categoria
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1.5 leading-relaxed">
                            Tem certeza que deseja excluir a categoria
                            <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="categoriaNome"></span>?
                            Esta ação não poderá ser desfeita e pode afetar lotes ou produtos vinculados a ela.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="modalExcluir = false"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 cursor-pointer transition-colors"
                    >
                        Cancelar
                    </button>

                    <form :action="acaoExcluir" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-red-600 hover:bg-red-700 text-white shadow-sm cursor-pointer transition-colors"
                        >
                            <span class="material-symbols-outlined text-lg leading-none">delete</span>
                            <span>Sim, excluir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection