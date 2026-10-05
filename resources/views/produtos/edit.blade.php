@extends('layouts.layout')
@section('title', 'Editar produto')

@section('conteudo')

{{-- Botão de Voltar --}}
<div class="mb-4" style="text-align: left !important;">
    <a href="{{ route('produtos.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para os produtos
    </a>
</div>


<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Editar Produto
    </h1>


    {{-- Erros de validação --}}
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

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nome do produto:
                </label>

                <input
                    type="text"
                    name="nome"
                    value="{{ old('nome', $produto->nome) }}"
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
                            {{ old('categoria_id', $produto->categoria_id) == $categoria->id ? 'selected' : '' }}
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
                        {{ old('unidade', $produto->unidade) == 'kg' ? 'selected' : '' }}
                    >
                        Kg
                    </option>

                    <option
                        value="saca"
                        {{ old('unidade', $produto->unidade) == 'saca' ? 'selected' : '' }}
                    >
                        Saca
                    </option>

                    <option
                        value="unidade"
                        {{ old('unidade', $produto->unidade) == 'unidade' ? 'selected' : '' }}
                    >
                        Unidade
                    </option>

                    <option
                        value="litro"
                        {{ old('unidade', $produto->unidade) == 'litro' ? 'selected' : '' }}
                    >
                        Litro
                    </option>

                    <option
                        value="tonelada"
                        {{ old('unidade', $produto->unidade) == 'tonelada' ? 'selected' : '' }}
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


        {{-- Imagem atual --}}
        @if($produto->imagem)

            <div class="mt-4">

                <p class="text-sm font-medium text-gray-700 mb-2">
                    Imagem atual:
                </p>

                <img
                    src="{{ asset('storage/' . $produto->imagem) }}"
                    alt="{{ $produto->nome }}"
                    class="w-32 h-32 object-cover rounded-lg border border-gray-200"
                >

                <p class="text-xs text-gray-500 mt-2">
                    Selecione uma nova imagem acima para substituí-la.
                </p>

            </div>

        @endif


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
            >{{ old('descricao', $produto->descricao) }}</textarea>

        </div>


        {{-- Botões --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 w-full">

            <a
                href="{{ route('produtos.show', $produto) }}"
                class="text-gray-600 hover:text-gray-900"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2 rounded-md transition-colors"
            >
                Salvar alterações
            </button>

        </div>

    </form>

</div>

@endsection