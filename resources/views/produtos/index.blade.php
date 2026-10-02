@extends('layouts.layout')
@section('title', 'Produtos')

@section('conteudo')

<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-7xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Produtos
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Produtos cadastrados no marketplace
            </p>
        </div>

        @auth
            <a
                href="{{ route('produtos.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors shadow-sm"
            >
                <span class="material-symbols-outlined text-xl">
                    add
                </span>

                Cadastrar produto
            </a>
        @endauth

    </div>


    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded-r-md">
            {{ session('sucesso') }}
        </div>
    @endif


    {{-- Lista de produtos --}}
    @if($produtos->count())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            @foreach($produtos as $produto)

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">

                    {{-- Imagem --}}
                    <div class="w-full h-48 bg-gray-100">

                        @if($produto->imagem)

                            <img
                                src="{{ asset('storage/' . $produto->imagem) }}"
                                alt="{{ $produto->nome }}"
                                class="w-full h-full object-cover"
                            >

                        @else

                            <div class="w-full h-full flex items-center justify-center text-gray-400">

                                <span class="material-symbols-outlined text-6xl">
                                    image
                                </span>

                            </div>

                        @endif

                    </div>


                    {{-- Conteúdo --}}
                    <div class="p-4">

                        {{-- Categoria --}}
                        <span class="inline-block text-xs font-semibold text-[#236350] bg-green-50 px-2.5 py-1 rounded-full mb-2">
                            {{ $produto->categoria->nome ?? 'Sem categoria' }}
                        </span>


                        {{-- Nome --}}
                        <h2 class="text-lg font-bold text-gray-800 line-clamp-1">
                            {{ $produto->nome }}
                        </h2>


                        {{-- Descrição --}}
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2 min-h-[40px]">
                            {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                        </p>


                        {{-- Informações --}}
                        <div class="mt-4 space-y-2 text-sm">

                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">
                                    Unidade
                                </span>

                                <span class="font-semibold text-gray-700">
                                    {{ $produto->unidade }}
                                </span>
                            </div>


                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">
                                    Fornecedor
                                </span>

                                <span class="font-semibold text-gray-700 truncate max-w-[150px]">
                                    {{ $produto->fornecedor->nome ?? 'Não informado' }}
                                </span>
                            </div>

                        </div>


                        {{-- Botões --}}
                        <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">

                        <a
                            href="{{ route('produtos.show', $produto) }}"
                            class="flex-1 flex items-center justify-center text-center bg-[#236350] hover:bg-[#1B4D3E] text-white px-3 py-2 rounded-md text-sm transition-colors"
                        >
                            Ver detalhes
                        </a>

                        @auth

                            @if(
                                auth()->user()->user_type === 'admin' ||
                                (
                                    auth()->user()->fornecedor &&
                                    auth()->user()->fornecedor->id === $produto->fornecedor_id
                                )
                            )

                                <a
                                    href="{{ route('produtos.edit', $produto) }}"
                                    class="flex items-center justify-center w-10 h-10 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                                    title="Editar"
                                >
                                    <span class="material-symbols-outlined text-lg">
                                        settings
                                    </span>
                                </a>

                            @endif

                        @endauth

                        </div>


                    </div>

                </div>

            @endforeach

        </div>

    @else

        {{-- Nenhum produto --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-10 text-center">

            <span class="material-symbols-outlined text-6xl text-gray-300">
                inventory_2
            </span>

            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhum produto cadastrado
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Ainda não existem produtos disponíveis no marketplace.
            </p>

            @auth
                <a
                    href="{{ route('produtos.create') }}"
                    class="inline-flex items-center gap-2 mt-5 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors"
                >
                    <span class="material-symbols-outlined">
                        add
                    </span>

                    Cadastrar primeiro produto
                </a>
            @endauth

        </div>

    @endif

</div>

@endsection