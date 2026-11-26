@extends('layouts.layout')

@section('title', 'Editar produto')

@section('conteudo')

@php
    $imagemAtual = $produto->imagem
        ? (str_contains($produto->imagem, 'assets/')
            ? asset($produto->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
        : asset('assets/milho.png');
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        {{-- Item 2: Produtos cadastrados --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a href="{{ route('produtos.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Produtos cadastrados
            </a>
        </li>

        {{-- Item 3: Página Atual --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] text-left">

    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">
        Editar Produto
    </h1>

    {{-- Erros de validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-300 rounded-r-md">

            <p class="font-bold">
                Atenção! Corrija os erros abaixo:
            </p>

            <ul class="mt-2 list-disc list-inside text-sm">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>
    @endif

    <form
        action="{{ route('produtos.update', $produto) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-4"
    >

        @csrf
        @method('PUT')

        {{-- Campos principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nome --}}
            <div>

                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Nome do produto:
                </label>

                <input
                    type="text"
                    name="nome"
                    id="nome"
                    value="{{ old('nome', $produto->nome) }}"
                    placeholder="Ex: Milho Verde"
                    required
                    class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >

            </div>

            {{-- Categoria --}}
            <div>

                <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Categoria:
                </label>

                <select
                    name="categoria_id"
                    id="categoria_id"
                    required
                    class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('categoria_id') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >

                    <option value="">
                        Selecione a categoria
                    </option>

                    @foreach($categorias as $categoria)

                        <option
                            value="{{ $categoria->id }}"
                            {{ (string) old('categoria_id', $produto->categoria_id) === (string) $categoria->id ? 'selected' : '' }}
                        >
                            {{ $categoria->nome }}
                        </option>

                    @endforeach

                </select>

            </div>

            {{-- Unidade --}}
            <div>

                <label for="unidade" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Unidade:
                </label>

                <select
                    name="unidade"
                    id="unidade"
                    required
                    class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('unidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >

                    <option value="">
                        Selecione a unidade
                    </option>

                    <option
                        value="kg"
                        {{ old('unidade', $produto->unidade) === 'kg' ? 'selected' : '' }}
                    >
                        Kg
                    </option>

                    <option
                        value="saca"
                        {{ old('unidade', $produto->unidade) === 'saca' ? 'selected' : '' }}
                    >
                        Saca
                    </option>

                    <option
                        value="unidade"
                        {{ old('unidade', $produto->unidade) === 'unidade' ? 'selected' : '' }}
                    >
                        Unidade
                    </option>

                    <option
                        value="litro"
                        {{ old('unidade', $produto->unidade) === 'litro' ? 'selected' : '' }}
                    >
                        Litro
                    </option>

                    <option
                        value="tonelada"
                        {{ old('unidade', $produto->unidade) === 'tonelada' ? 'selected' : '' }}
                    >
                        Tonelada
                    </option>

                </select>

            </div>

            {{-- Imagem --}}
            <div>

                <label for="imagem" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Imagem do produto:
                </label>

                <input
                    type="file"
                    name="imagem"
                    id="imagem"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="file-input w-full rounded-lg bg-white dark:bg-[#161f30] text-gray-700 dark:text-gray-200 border {{ $errors->has('imagem') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    JPG, PNG ou WEBP. Máximo de 2 MB.
                </p>

            </div>

        </div>

        {{-- Imagem atual --}}
        @if($produto->imagem)

            <div class="mt-4">

                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Imagem atual:
                </p>

                <img
                    src="{{ $imagemAtual }}"
                    alt="{{ $produto->nome ?? 'Produto' }}"
                    class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-[#273449] bg-gray-100 dark:bg-slate-800"
                    onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                >

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Selecione uma nova imagem acima para substituí-la.
                </p>

            </div>

        @endif

        {{-- Descrição --}}
        <div class="mt-4">

            <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Descrição:
            </label>

            <textarea
                name="descricao"
                id="descricao"
                rows="5"
                placeholder="Descreva o produto, suas características e informações importantes..."
                class="w-full p-3 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
            >{{ old('descricao', $produto->descricao) }}</textarea>

        </div>

        {{-- Botões --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-[#273449] w-full">

            <a
                href="{{ route('produtos.show', $produto) }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="btn-copav inline-flex items-center justify-center text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-95 cursor-pointer"
            >
                <span>Salvar alterações</span>
            </button>

        </div>

    </form>

</div>

@endsection