@extends('layouts.layout')
@section('title', 'Cadastrar produto')

@section('conteudo')

<div class="text-gray-500 mx-1 mt-1">
    <a
        href="{{ route('home') }}"
        class="inline-flex items-center gap-1 hover:text-gray-700"
    >
        <span class="material-symbols-outlined">arrow_back</span>
        Voltar para os produtos
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Cadastrar Novo Produto
    </h1>

    {{-- Bloco para exibição de erros de validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">

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
        action="{{ route('produtos.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-4"
    >

        @csrf


        {{-- Campos principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nome --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nome do produto:
                </label>

                <input
                    type="text"
                    name="nome"
                    value="{{ old('nome') }}"
                    placeholder="Ex: Milho Verde"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
                >
            </div>


            {{-- Categoria --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Categoria:
                </label>

                <select
                    name="categoria_id"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
                >

                    <option value="">
                        Selecione a categoria
                    </option>

                    @foreach($categorias as $categoria)

                        <option
                            value="{{ $categoria->id }}"
                            {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}
                        >
                            {{ $categoria->nome }}
                        </option>

                    @endforeach

                </select>
            </div>


            {{-- Unidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Unidade:
                </label>

                <select
                    name="unidade"
                    required
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
                >

                    <option value="">
                        Selecione a unidade
                    </option>

                    <option
                        value="kg"
                        {{ old('unidade') == 'kg' ? 'selected' : '' }}
                    >
                        Kg
                    </option>

                    <option
                        value="saca"
                        {{ old('unidade') == 'saca' ? 'selected' : '' }}
                    >
                        Saca
                    </option>

                    <option
                        value="unidade"
                        {{ old('unidade') == 'unidade' ? 'selected' : '' }}
                    >
                        Unidade
                    </option>

                    <option
                        value="litro"
                        {{ old('unidade') == 'litro' ? 'selected' : '' }}
                    >
                        Litro
                    </option>

                    <option
                        value="tonelada"
                        {{ old('unidade') == 'tonelada' ? 'selected' : '' }}
                    >
                        Tonelada
                    </option>

                </select>
            </div>


            {{-- Imagem --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Imagem do produto:
                </label>

                <input
                    type="file"
                    name="imagem"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="file-input w-full border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
                >

                <p class="text-xs text-gray-500 mt-1">
                    JPG, PNG ou WEBP. Máximo de 2 MB.
                </p>
            </div>

        </div>


        {{-- Descrição --}}
        <div class="mt-4">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descrição:
            </label>

            <textarea
                name="descricao"
                rows="5"
                placeholder="Descreva o produto, suas características e informações importantes..."
                class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
            >{{ old('descricao') }}</textarea>

        </div>


        {{-- Botões --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 w-full">

            <a
                href="{{ route('produtos.index') }}"
                class="text-gray-600 hover:text-gray-900"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2 rounded-md transition-colors"
            >
                Cadastrar produto
            </button>

        </div>

    </form>

</div>

@endsection