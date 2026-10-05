@extends('layouts.layout')
@section('title', $produto->nome)

@section('conteudo')

<div class="mb-4">
    <a href="{{ route('produtos.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-5xl mx-auto my-6">
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- Imagem --}}
            <div class="bg-gray-100 min-h-[350px]">
                <img
                    src="{{ $produto->imagem 
                        ? (str_contains($produto->imagem, 'assets/') 
                            ? asset($produto->imagem) 
                            : asset('storage/' . str_replace('public/', '', $produto->imagem))) 
                        : asset('assets/milho.png') }}"
                    alt="{{ $produto->nome ?? 'Imagem do produto' }}"
                    class="w-full h-full min-h-[350px] object-cover"
                    onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                />
            </div>

            {{-- Informações --}}
            <div class="p-6 md:p-8 flex flex-col">

                {{-- Categoria --}}
                <div class="mb-3">
                    <span class="inline-block text-xs font-semibold text-[#236350] bg-green-50 px-3 py-1 rounded-full">
                        {{ $produto->categoria->nome ?? 'Sem categoria' }}
                    </span>
                </div>

                {{-- Nome --}}
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $produto->nome }}
                </h1>

                {{-- Descrição --}}
                <div class="mt-5">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Descrição
                    </h2>
                    <p class="text-gray-700 mt-2 leading-relaxed">
                        {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                    </p>
                </div>

                {{-- Informações --}}
                <div class="mt-6 border-t border-gray-100 pt-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">
                            Unidade
                        </span>
                        <span class="font-semibold text-gray-800">
                            {{ $produto->unidade }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">

                        <span class="text-gray-500">
                            Fornecedor
                        </span>

                        <span class="font-semibold text-gray-800">
                            {{ $produto->fornecedor->nome ?? 'Não informado' }}
                        </span>

                    </div>
                </div>

                {{-- Botões --}}
                <div class="flex gap-3 mt-auto pt-8">

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
                                class="flex-1 text-center border border-gray-300 text-gray-700 hover:bg-gray-100 px-4 py-2.5 rounded-md transition-colors">
                                Editar produto
                            </a>

                        @endif

                    @endauth

                    <a
                        href="{{ route('home') }}"
                        class="flex-1 text-center bg-[#236350] hover:bg-[#1B4D3E] text-white px-4 py-2.5 rounded-md transition-colors">
                        Voltar para o início
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Ofertas deste produto --}}
    @if($produto->ofertas && $produto->ofertas->count())

        <div class="mt-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Ofertas deste produto
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($produto->ofertas as $oferta)

                    <a
                        href="{{ route('ofertas.show', $oferta) }}"
                        class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">

                        <div class="flex justify-between items-start gap-3">

                            <div>
                                <p class="font-bold text-gray-800">
                                    {{ $oferta->quantidade }} {{ $oferta->unidade }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $oferta->localizacao ?? 'Localização não informada' }}
                                </p>
                            </div>

                            <span class="font-bold text-[#79A961]">
                                R$ {{ number_format($oferta->valor, 2, ',', '.') }}
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-gray-500">
                            Status:
                            <span class="font-semibold">
                                {{ ucfirst($oferta->status) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection