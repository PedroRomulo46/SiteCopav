@extends('layouts.layout')

@section('title', 'Editar Categoria')

@section('conteudo')

{{-- Breadcrumbs --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E] transition-colors">
                Início
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{ route('categorias.index') }}" class="hover:text-[#1B4D3E] transition-colors">
                Categorias
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                Editar
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">edit_square</span>
                Editar Categoria
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Atualize as informações da categoria <span class="font-semibold text-gray-700">{{ $categoria->nome }}</span>
            </p>
        </div>
    </div>

    {{-- Erros de Validação --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 text-red-800 rounded-r-md">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <span class="material-symbols-outlined text-xl">error</span>
                <span>Verifique os campos abaixo:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1 ml-1">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário de Edição --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome da Categoria --}}
            <div>
                <label for="nome" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nome da Categoria <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    value="{{ old('nome', $categoria->nome) }}"
                    required
                    placeholder="Ex: Pescados, Hortifruti, Grãos..."
                    class="input input-bordered w-full bg-white text-gray-800 focus:outline-none focus:border-[#1B4D3E] @error('nome') border-red-500 @enderror"
                >
                @error('nome')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block text-sm font-semibold text-gray-700 mb-2">
                    Descrição
                </label>
                <textarea 
                    id="descricao" 
                    name="descricao" 
                    rows="4"
                    placeholder="Descreva brevemente o tipo de itens que pertencem a esta categoria..."
                    class="textarea textarea-bordered w-full bg-white text-gray-800 focus:outline-none focus:border-[#1B4D3E] @error('descricao') border-red-500 @enderror"
                >{{ old('descricao', $categoria->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botões de Ação --}}
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('categorias.index') }}" class="btn btn-ghost text-gray-700 hover:bg-gray-100">
                    Cancelar
                </a>

                <button 
                    type="submit" 
                    class="btn bg-[#1B4D3E] hover:bg-[#143B2F] text-white border-none gap-2 shadow-sm px-6 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-xl">save</span>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</div>

@endsection