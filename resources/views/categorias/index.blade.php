@extends('layouts.layout')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('demandas.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#1B4D3E] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

    {{-- Alerta de Sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md text-left flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">check_circle</span>
                <span>{{ session('sucesso') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="btn btn-xs btn-circle btn-ghost text-emerald-800">✕</button>
        </div>
    @endif

    {{-- Cabeçalho da Página --}}
    <div class="flex flex-row items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
        <div class="text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">category</span>
                Lista de Categorias
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Gerencie as categorias de produtos cadastradas no sistema
            </p>
        </div>

        <a href="{{ route('categorias.create') }}" class="btn bg-[#1B4D3E] hover:bg-[#143B2F] text-white border-none gap-2 shadow-sm shrink-0 font-medium">
            <span class="material-symbols-outlined text-xl">add</span>
            Cadastrar Categoria
        </a>
    </div>

    {{-- Grid de Categorias --}}
    @if($categorias->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($categorias as $categoria)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-between overflow-hidden">
                    
                    {{-- Conteúdo do Card --}}
                    <div class="p-5 text-left">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h2 class="text-xl font-semibold text-gray-800 hover:text-[#1B4D3E] transition-colors">
                                {{ $categoria->nome }}
                            </h2>
                            <span class="badge bg-emerald-50 text-[#1B4D3E] border-emerald-200 text-xs py-2 px-2.5 font-medium">
                                ID #{{ $categoria->id }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 line-clamp-3">
                            {{ $categoria->descricao ?? 'Sem descrição informada.' }}
                        </p>
                    </div>

                    {{-- Ações do Card --}}
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                        
                        <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-ghost btn-xs text-[#1B4D3E] hover:bg-emerald-50 gap-1 font-medium">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            Ver
                        </a>

                        <div class="flex items-center gap-1">
                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-ghost btn-xs text-amber-600 hover:bg-amber-50 gap-1 font-medium">
                                <span class="material-symbols-outlined text-base">edit</span>
                                Editar
                            </a>

                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');" class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-ghost btn-xs text-red-600 hover:bg-red-50 gap-1 font-medium">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                    Excluir
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
            @endforeach
        </div>
    @else
        {{-- Estado Vazio --}}
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

</div>

@endsection