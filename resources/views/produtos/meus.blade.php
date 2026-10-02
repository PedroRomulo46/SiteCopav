@extends('layouts.layout')

@section('title', 'Meus produtos')

@section('conteudo')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Meus produtos
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Produtos cadastrados pela sua empresa
            </p>
        </div>

        <a
            href="{{ route('produtos.create') }}"
            class="inline-flex items-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors"
        >
            <span class="material-symbols-outlined">
                add
            </span>

            Novo produto
        </a>

    </div>


    @if($produtos->count())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            @foreach($produtos as $produto)

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">

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


                    <div class="p-4">

                        <span class="inline-block text-xs font-semibold text-[#236350] bg-green-50 px-2.5 py-1 rounded-full mb-2">
                            {{ $produto->categoria->nome ?? 'Sem categoria' }}
                        </span>

                        <h2 class="text-lg font-bold text-gray-800">
                            {{ $produto->nome }}
                        </h2>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                        </p>

                        <div class="mt-4 flex items-center justify-between text-sm">

                            <span class="text-gray-500">
                                Unidade
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ $produto->unidade }}
                            </span>

                        </div>


                        <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">

                            <a
                                href="{{ route('produtos.show', $produto) }}"
                                class="flex-1 flex items-center justify-center bg-[#236350] hover:bg-[#1B4D3E] text-white px-3 py-2 rounded-md text-sm transition-colors"
                            >
                                Ver detalhes
                            </a>

                            <a
                                href="{{ route('produtos.edit', $produto) }}"
                                class="flex items-center justify-center w-10 h-10 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                                title="Editar"
                            >
                                <span class="material-symbols-outlined text-lg">
                                    settings
                                </span>
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-10 text-center">

            <span class="material-symbols-outlined text-6xl text-gray-300">
                inventory_2
            </span>

            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhum produto cadastrado
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Sua empresa ainda não possui produtos cadastrados.
            </p>

            <a
                href="{{ route('produtos.create') }}"
                class="inline-flex items-center gap-2 mt-5 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors"
            >
                <span class="material-symbols-outlined">
                    add
                </span>

                Cadastrar produto
            </a>

        </div>

    @endif

</div>

@endsection