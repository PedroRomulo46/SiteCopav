@extends('layouts.layout')
@section('title', $produto->nome ?? 'Detalhes da Oferta')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    {{-- Exibição de Alertas de Sucesso / Erro --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-red-500">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Coluna 1: Imagem do Produto -->
        <div class="lg:col-span-5 lg:sticky lg:top-6">
            <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <img
                    class="w-full h-[380px] sm:h-[450px] object-cover rounded-lg"
                    src="{{ $produto->imagem 
                        ? (str_contains($produto->imagem, 'assets/') 
                            ? asset($produto->imagem) 
                            : asset('storage/' . str_replace('public/', '', $produto->imagem))) 
                        : asset('assets/milho.png') }}"
                    alt="{{ $produto->nome ?? 'Imagem do produto' }}"
                />
            </div>
        </div>

        <!-- Coluna 2: Informações Técnicas e Descrição -->
        <div class="lg:col-span-4 flex flex-col gap-4 bg-white p-3 rounded-md">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">
                    {{ $produto->nome ?? 'Produto sem nome' }}
                </h1>
                
                <!-- Link do Fornecedor -->
                <a href="#" class="inline-block mt-1 text-xs font-semibold text-[#236350] hover:underline">
                    Loja de {{ $oferta->fornecedor->nome ?? 'Fornecedor Copav' }}
                </a>
            </div>

            <!-- Avaliações -->
            <div class="flex items-center gap-2 py-3 px-2 rounded-lg">
                <div class="flex items-center gap-1">
                    <span class="text-sm font-bold text-amber-400">4.6</span>
                    <div class="flex text-amber-400 text-base">
                        ★★★★<span class="text-gray-300">★</span>
                    </div>
                </div>
                <span class="text-xs text-gray-500">(1.889 avaliações)</span>
            </div>

            <!-- Preço e Medida da Oferta -->
            <div class="space-y-1">
                <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Preço unitário</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-bold text-gray-700">R$</span>
                    <span class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                        {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                    </span>
                    <span class="text-sm font-medium text-gray-500">
                        / {{ $oferta->unidade ?? 'Unidade' }}
                    </span>
                </div>
            </div>

            <!-- Descrição detalhada -->
            <div class="pt-2 border-t border-gray-200">
                <h3 class="font-semibold text-sm text-gray-900 uppercase tracking-wider">Sobre este item</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $produto->descricao ?? 'Nenhuma descrição detalhada informada para este produto.' }}
                </p>
            </div>
        </div>

        <!-- Coluna 3: Box de Compra / Ações Comerciais -->
        <div class="lg:col-span-3">
            <div class="border border-gray-200 rounded-xl p-5 flex flex-col gap-4 shadow-sm bg-white">
                
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Valor da oferta</span>
                    <div class="text-2xl font-bold text-gray-900">
                        R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-md font-medium border border-emerald-100">
                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                    <span>
                        Em estoque: 
                        <strong>
                            @if(isset($oferta->quantidade))
                                @if(strtolower($oferta->unidade) === 'saca')
                                    {{ number_format($oferta->quantidade, 0, ',', '.') }}
                                @else
                                    {{ fmod($oferta->quantidade, 1) == 0 ? number_format($oferta->quantidade, 0, ',', '.') : number_format($oferta->quantidade, 2, ',', '.') }}
                                @endif
                            @else
                                0
                            @endif
                            {{ $oferta->unidade ?? '' }}
                        </strong>
                    </span>
                </div>

                <div class="text-xs text-gray-600 space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Enviado por:</span>
                        <span class="text-gray-800 font-semibold">Copav</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Vendido por:</span>
                        <span class="text-gray-800 font-semibold">{{ $oferta->fornecedor->nome ?? 'Fornecedor' }}</span>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Botões de Ação -->
                <div class="flex flex-col gap-2.5">
                    @auth
                        <form action="{{ route('negociacoes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="oferta_id" value="{{ $oferta->id }}">
                            <button
                                type="submit"
                                class="w-full bg-[#236350] hover:bg-[#1B4D3E] text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm hover:shadow flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">currency_exchange</span>
                                Negociar Preço
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="w-full bg-[#236350] hover:bg-[#1B4D3E] text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm text-center flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-base">login</span>
                            Entrar para negociar
                        </a>
                    @endauth

                    <button
                        type="button"
                        class="w-full bg-gray-100 text-gray-400 text-sm font-semibold py-2.5 px-4 rounded-lg cursor-not-allowed border border-gray-200 flex items-center justify-center gap-2"
                        disabled>
                        <span class="material-symbols-outlined text-base">shopping_bag</span>
                        Comprar Agora
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection