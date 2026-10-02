@extends('layouts.layout')
@section('title', 'Ofertas')

@section('conteudo')

<div class="text-gray-500 mx-1 mt-1">
    <a
        href="{{ route('home') }}"
        class="inline-flex items-center gap-1 hover:text-gray-700"
    >
        <span class="material-symbols-outlined">
            arrow_back
        </span>


    Voltar para o início
</a>

</div>

<div class="max-w-7xl mx-auto">

{{-- Cabeçalho --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Ofertas
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Ofertas de produtos cadastradas no marketplace
        </p>
    </div>


    @auth
        <a
            href="{{ route('ofertas.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors shadow-sm"
        >
            <span class="material-symbols-outlined text-xl">
                add
            </span>

            Cadastrar oferta
        </a>
    @endauth

</div>


{{-- Mensagem de sucesso --}}
@if(session('sucesso'))

    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded-r-md">
        {{ session('sucesso') }}
    </div>

@endif


{{-- Lista de ofertas --}}
@if($ofertas->count())

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

        @foreach($ofertas as $oferta)

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">

                {{-- Imagem do produto --}}
                <div class="w-full h-48 bg-gray-100">

                    @if($oferta->produto && $oferta->produto->imagem)

                        <img
                            src="{{ asset('storage/' . $oferta->produto->imagem) }}"
                            alt="{{ $oferta->produto->nome }}"
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

                    {{-- Status --}}
                    @php

                        $statusClasses = [
                            'rascunho' => 'text-gray-600 bg-gray-100',
                            'publicada' => 'text-green-700 bg-green-50',
                            'encerrada' => 'text-yellow-700 bg-yellow-50',
                            'cancelada' => 'text-red-700 bg-red-50',
                        ];

                        $statusClass =
                            $statusClasses[$oferta->status]
                            ?? 'text-gray-600 bg-gray-100';

                    @endphp


                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full mb-2 {{ $statusClass }}">

                        {{ ucfirst($oferta->status) }}

                    </span>


                    {{-- Produto --}}
                    <h2 class="text-lg font-bold text-gray-800 line-clamp-1">

                        {{ $oferta->produto->nome ?? 'Produto não informado' }}

                    </h2>


                    {{-- Fornecedor --}}
                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">

                        {{ $oferta->fornecedor->nome ?? 'Fornecedor não informado' }}

                    </p>


                    {{-- Informações --}}
                    <div class="mt-4 space-y-2 text-sm">

                        {{-- Quantidade --}}
                        <div class="flex items-center justify-between">

                            <span class="text-gray-500">
                                Quantidade
                            </span>

                            <span class="font-semibold text-gray-700">
                                {{ $oferta->quantidade }} {{ $oferta->unidade }}
                            </span>

                        </div>


                        {{-- Valor --}}
                        <div class="flex items-center justify-between">

                            <span class="text-gray-500">
                                Valor
                            </span>

                            <span class="font-bold text-[#236350]">

                                R$
                                {{ number_format($oferta->valor, 2, ',', '.') }}

                            </span>

                        </div>


                        {{-- Localização --}}
                        <div class="flex items-center justify-between gap-2">

                            <span class="text-gray-500">
                                Localização
                            </span>

                            <span class="font-semibold text-gray-700 truncate max-w-[170px]">

                                {{ $oferta->localizacao ?? 'Não informada' }}

                            </span>

                        </div>

                    </div>


                    {{-- Botões --}}
                    <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">

                        <a
                            href="{{ route('ofertas.show', $oferta) }}"
                            class="flex-1 flex items-center justify-center text-center bg-[#236350] hover:bg-[#1B4D3E] text-white px-3 py-2 rounded-md text-sm transition-colors"
                        >
                            Ver detalhes
                        </a>

                        @auth

                            @if(
                                auth()->user()->user_type === 'admin' ||
                                (
                                    auth()->user()->fornecedor &&
                                    auth()->user()->fornecedor->id === $oferta->fornecedor_id
                                )
                            )

                                <a
                                    href="{{ route('ofertas.edit', $oferta) }}"
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

    {{-- Nenhuma oferta --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-10 text-center">

        <span class="material-symbols-outlined text-6xl text-gray-300">
            local_offer
        </span>


        <h2 class="text-xl font-bold text-gray-700 mt-4">
            Nenhuma oferta cadastrada
        </h2>


        <p class="text-gray-500 text-sm mt-2">
            Ainda não existem ofertas cadastradas no marketplace.
        </p>


        @auth

            <a
                href="{{ route('ofertas.create') }}"
                class="inline-flex items-center gap-2 mt-5 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors"
            >

                <span class="material-symbols-outlined">
                    add
                </span>

                Cadastrar primeira oferta

            </a>

        @endauth

    </div>

@endif

</div>

@endsection
