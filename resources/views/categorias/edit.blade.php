@extends('layouts.layout')

@section('title', 'Editar Categoria')

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
            <a
                href="{{ route('categorias.index') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
            >
                Categorias
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                Editar
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">edit_square</span>
                <span>Editar Categoria</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Atualize as informações da categoria
                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $categoria->nome }}</span>
            </p>
        </div>
    </div>

    {{-- Erros de Validação --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-600 dark:border-red-400 text-red-800 dark:text-red-300 rounded-r-md">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <span class="material-symbols-outlined text-xl leading-none">error</span>
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
    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 sm:p-8">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome da Categoria --}}
            <div>
                <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Nome da Categoria <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="{{ old('nome', $categoria->nome) }}"
                    required
                    placeholder="Ex: Pescados, Hortifruti, Grãos..."
                    class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                >
                @error('nome')
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Descrição
                </label>
                <textarea
                    id="descricao"
                    name="descricao"
                    rows="4"
                    placeholder="Descreva brevemente o tipo de itens que pertencem a esta categoria..."
                    class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                >{{ old('descricao', $categoria->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botões de Ação --}}
            <div class="pt-4 border-t border-gray-100 dark:border-[#273449] flex items-center justify-end gap-3">
                <a
                    href="{{ route('categorias.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn-copav inline-flex items-center justify-center gap-2 px-6 py-2 rounded-lg text-sm font-medium shadow-sm cursor-pointer transition-colors"
                >
                    <span class="material-symbols-outlined text-xl leading-none">save</span>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection