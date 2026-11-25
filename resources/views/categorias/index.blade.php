@extends('layouts.layout')

@section('title', 'Categorias')

@section('conteudo')

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
    <nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">

            {{-- item 1: início --}}
            <li>
                <a href="{{ route('home') }}">
                    Início
                </a>
            </li>

            {{-- item 2: página atual --}}
            <li class="flex items-center space-x-2 hover:cursor-pointer">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <span class="font-semibold text-gray-800">
                    @yield('title')
                </span>
            </li>
        </ol>
    </nav>

    <div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

        {{-- alerta de sucesso --}}
        @if(session('sucesso'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md text-left flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    <span>{{ session('sucesso') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="btn btn-xs btn-circle btn-ghost text-emerald-800">✕</button>
            </div>
        @endif

        {{-- cabeçalho da página --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
            <div class="text-left">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">category</span>
                    Lista de Categorias
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gerencie as categorias de produtos cadastradas no sistema
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('categorias.sugerir') }}" class="btn btn-outline border-[#1B4D3E] text-[#1B4D3E] hover:bg-emerald-50 hover:border-[#1B4D3E] gap-2 font-medium">
                    <span class="material-symbols-outlined text-xl">lightbulb</span>
                    Sugerir Categoria
                </a>

                <a href="{{ route('categorias.create') }}" class="btn btn-copav text-white border-none gap-2 shadow-sm font-medium">
                    <span class="material-symbols-outlined text-xl">add</span>
                    Cadastrar Categoria
                </a>
            </div>
        </div>

        {{-- grid de categorias --}}
        @if($categorias->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categorias as $categoria)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-between overflow-hidden">
                        
                        {{-- conteúdo do card --}}
                        <div class="p-5 text-left">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <h2 class="text-xl font-semibold text-gray-800 hover:text-[#1B4D3E] transition-colors">
                                    {{ $categoria->nome }}
                                </h2>
                            </div>

                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ $categoria->descricao ?? 'Sem descrição informada.' }}
                            </p>
                        </div>

                        {{-- ações do card --}}
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                            
                            <a href="{{ route('categorias.show', [$categoria, 'origem' => 'index']) }}" class="btn btn-ghost btn-xs text-[#1B4D3E] hover:bg-emerald-50 gap-1 font-medium">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Ver
                            </a>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-ghost btn-xs text-amber-600 hover:bg-amber-50 gap-1 font-medium">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                    Editar
                                </a>

                                <button 
                                    type="button"
                                    @click="confirmarExclusao(@js($categoria->nome), '{{ route('categorias.destroy', $categoria) }}')"
                                    class="btn btn-ghost btn-xs text-red-600 hover:bg-red-50 gap-1 font-medium cursor-pointer"
                                >
                                    <span class="material-symbols-outlined text-base">delete</span>
                                    Excluir
                                </button>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>
        @else
            {{-- estado vazio --}}
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300 p-8 my-6">
                <div class="w-16 h-16 bg-emerald-50 text-[#1B4D3E] rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">category</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhuma categoria cadastrada</h3>
                <p class="text-sm text-gray-500 mb-6">Comece adicionando a primeira categoria para organizar seus produtos.</p>
                
                <a href="{{ route('categorias.create') }}" class="btn bg-[#1B4D3E] hover:bg-[#143B2F] text-white border-none gap-2">
                    <span class="material-symbols-outlined text-xl">add</span>
                    Cadastrar Categoria
                </a>
            </div>
        @endif

        {{-- seção de sugestões enviadas pelos usuários --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-2xl">lightbulb</span>
                        Sugestões de Categorias
                        @if(isset($sugestoes) && $sugestoes->isNotEmpty())
                            <span class="badge bg-amber-100 text-amber-800 border-none font-semibold">
                                {{ $sugestoes->count() }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Sugestões enviadas pelos usuários aguardando avaliação
                    </p>
                </div>
            </div>

            @if(isset($sugestoes) && $sugestoes->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($sugestoes as $sugestao)
                        <div class="bg-white rounded-xl border border-amber-200/80 shadow-sm flex flex-col justify-between overflow-hidden">
                            <div class="p-5 text-left">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $sugestao->nome }}
                                    </h3>
                                    <span class="badge badge-warning badge-sm badge-outline shrink-0">Pendente</span>
                                </div>

                                <p class="text-sm text-gray-600 line-clamp-3 mb-3">
                                    {{ $sugestao->descricao ?? 'Sem descrição informada.' }}
                                </p>

                                <div class="text-xs text-gray-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">person</span>
                                    <span>{{ $sugestao->user->nome ?? 'Usuário' }}</span>
                                    <span>•</span>
                                    <span>{{ $sugestao->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- ações para aprovar ou recusar --}}
                            <div class="px-5 py-3 bg-amber-50/40 border-t border-amber-100 flex items-center justify-between gap-2">
                                <form action="{{ route('categorias.sugerir.aprovar', $sugestao) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-xs text-emerald-700 hover:bg-emerald-50 gap-1 font-medium cursor-pointer">
                                        <span class="material-symbols-outlined text-base">check</span>
                                        Aprovar
                                    </button>
                                </form>

                                <form action="{{ route('categorias.sugerir.recusar', $sugestao) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-red-600 hover:bg-red-50 gap-1 font-medium cursor-pointer">
                                        <span class="material-symbols-outlined text-base">close</span>
                                        Recusar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                    Nenhuma sugestão de categoria pendente no momento.
                </div>
            @endif
        </div>

    </div>

    {{-- modal de confirmação de exclusão --}}
    <div 
        x-show="modalExcluir" 
        x-cloak
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="modalExcluir = false"
    >
        <div 
            x-show="modalExcluir"
            x-transition.scale.95.duration.150ms
            class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full p-6 text-left"
        >
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900">
                        Excluir categoria
                    </h3>
                    <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">
                        Tem certeza que deseja excluir a categoria 
                        <span class="font-semibold text-gray-900" x-text="categoriaNome"></span>? 
                        Esta ação não poderá ser desfeita e pode afetar lotes ou produtos vinculados a ela.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    @click="modalExcluir = false" 
                    class="btn btn-ghost text-gray-700 hover:bg-gray-100"
                >
                    Cancelar
                </button>

                <form :action="acaoExcluir" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="btn bg-red-600 hover:bg-red-700 text-white border-none gap-1.5 shadow-sm cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Sim, excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection