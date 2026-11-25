@extends('layouts.layout')

@section('title', 'Meus produtos')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-7xl mx-auto text-left">

    <div class="flex items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">

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
            class="inline-flex items-center gap-2 btn-copav text-white px-5 py-2.5 rounded-md text-sm font-semibold transition-opacity hover:opacity-90 shrink-0"
        >
            <span class="material-symbols-outlined">
                add
            </span>

            Novo produto
        </a>

    </div>

    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded-r-md">
            {{ session('sucesso') }}
        </div>
    @endif

    @if($produtos->count())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            @foreach($produtos as $produto)
                @php
                    $imagemProduto = $produto->imagem
                        ? (str_contains($produto->imagem, 'assets/')
                            ? asset($produto->imagem)
                            : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
                        : null;
                @endphp

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">

                    <div class="w-full h-48 bg-gray-100 overflow-hidden">

                        @if($imagemProduto)

                            <img
                                src="{{ $imagemProduto }}"
                                alt="{{ $produto->nome ?? 'Produto' }}"
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
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
                            {{ $produto->categoria?->nome ?? 'Sem categoria' }}
                        </span>

                        <h2 class="text-lg font-bold text-gray-800 line-clamp-1">
                            {{ $produto->nome ?? 'Produto sem nome' }}
                        </h2>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                        </p>

                        <div class="mt-4 flex items-center justify-between text-sm">

                            <span class="text-gray-500">
                                Unidade
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ $produto->unidade ?? '—' }}
                            </span>

                        </div>

                        <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">

                            <a
                                href="{{ route('produtos.show', $produto) }}"
                                class="btn-copav flex-1 flex items-center justify-center text-white px-3 py-2 rounded-md text-sm font-semibold transition-opacity hover:opacity-90"
                            >
                                Ver detalhes
                            </a>

                            <a
                                href="{{ route('produtos.edit', $produto) }}"
                                class="flex items-center justify-center w-10 h-10 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md transition-colors"
                                title="Editar"
                                aria-label="Editar produto"
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
                class="btn-copav inline-flex items-center gap-2 mt-5 text-white px-5 py-2.5 rounded-md text-sm font-semibold transition-opacity hover:opacity-90"
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