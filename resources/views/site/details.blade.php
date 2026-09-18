@extends('site.layout')
@section('title', 'Detalhes da Oferta')

@section('conteudo')

<div class="text-gray-500 mx-4 mt-2 ">
    <a href="{{ route('home') }}" class="inline-flex gap-1">
        <span class="material-symbols-outlined">arrow_back</span>
        Voltar para os produtos
    </a>
</div>

<div class="bg-white min-h-screen p-4 md:p-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Coluna com a Imagem do Produto (4 colunas) -->
        <div class="lg:col-span-5 flex justify-center items-start sticky top-4">
            <div class="rounded-lg p-2 w-full bg-white">
                <img class="w-full h-auto max-h-[450px] object-contain rounded-lg" src="{{ asset('assets/milho.png') }}" alt="{{ $produto->nome }}" />
            </div>
        </div>

        <!-- Coluna com as Informações do Produto (4 colunas) -->
        <div class="lg:col-span-4 flex flex-col gap-2">
            <h1 class="text-xl md:text-2xl font-medium text-gray-900 leading-tight">
                {{ $produto->nome }}
            </h1>
            
            <!-- Loja do fornecedor -->
            <a href="#" class="text-xs text-blue-600 hover:underline">Visite a loja do fornecedor</a>

            <!-- Avaliações simuladas -->
            <div class="flex items-center gap-1 text-sm border-b border-gray-200 pb-3">
                <a href="">
                    <span class="text-yellow-500 text-md font-bold">4.6</span>
                    <span class="text-yellow-400 text-lg">★★★★☆</span>
                    <span class="text-blue-600 text-xs ml-1">(1.889 avaliações)</span>
                </a>
            </div>

            <!-- Preço e Unidade -->
            <div class="py-2 border-b border-gray-200">
                <div class="flex items-baseline gap-1">
                    <span class="text-xs text-gray-500">R$</span>
                    <span class="text-3xl font-semibold text-gray-900">{{ number_format($oferta->valor ?? 0, 2, ',', '.') }}</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">Vendido em: <span class="font-bold text-gray-800">{{ $produto->unidade }}</span></p>
            </div>

            <hr class="my-2 border-gray-300">

            <!-- Descrição em Tópicos -->
            <div class="mt-2">
                <h3 class="font-bold text-md text-gray-900 mb-2">Sobre este item:</h3>
                <p class="text-md text-gray-700 leading-relaxed">
                    {{ $produto->descricao }}
                </p>
            </div>
        </div>

        <!-- Coluna 3: Box de Compra/Ações (3 colunas) -->
        <div class="lg:col-span-3">
            <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-3 shadow-sm bg-white">
                <div class="text-2xl font-semibold text-gray-900">
                    R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                </div>

                <div class="text-xs text-emerald-700 font-bold">
                    Em estoque
                </div>

                <div class="text-xs text-gray-500">
                    Enviado por <span class="text-gray-800 font-semibold">Copav</span><br>
                    Vendido por <span class="text-gray-800 font-semibold">{{ $oferta->fornecedor->nome ?? 'Fornecedor' }}</span>
                </div>

                <hr class="my-2 border-gray-300">

                <!-- Botões de Ação -->
                <div class="flex flex-col gap-2 mt-3">
                    <button class="w-full bg-[#79A961] hover:bg-[#709b58] text-white text-xs font-medium py-2 px-4 rounded-full transition-colors shadow-sm">
                        Negociar Preço
                    </button>
                    
                    <button class="w-full bg-[#79A961] hover:bg-[#709b58] text-white text-xs font-medium py-2 px-4 rounded-full transition-colors shadow-sm">
                        Comprar Agora
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection