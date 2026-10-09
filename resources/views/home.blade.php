@extends('layouts.layout')

@section('title', 'Home')

@section('conteudo')

{{-- Script Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
</style>

<div 
    x-data="{ abaAtiva: 'lotes' }"
    class="w-full max-w-7xl mx-auto px-2 sm:px-4 py-4 space-y-6 bg-[#ebeae7] min-h-screen">

    {{-- Painel do fornecedor --}}
    @auth
        <div class="bg-[#123228] text-white p-3 sm:p-5 rounded-2xl shadow-md">
            
            {{-- Se for fornecedor --}}
            @if(auth()->user()->fornecedor)
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3 pb-3 border-b border-emerald-800">
                    <div>
                        <span class="text-xs text-white font-semibold uppercase tracking-wider block">Painel do Fornecedor</span>
                        <h2 class="text-base sm:text-lg font-bold">Gestão dos seus Lotes</h2>
                    </div>

                    {{-- Botões de Ação no Topo --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('ofertas.create') }}" 
                           class="btn-copav text-xs sm:text-sm px-3 sm:px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-1 shadow-sm flex-1 md:flex-initial transition-transform active:scale-95">
                            <span class="material-symbols-outlined text-base">add</span>
                            <span>Nova Oferta</span>
                        </a>
                        <a href="{{ route('produtos.index') }}" 
                           class="btn-copav text-white text-xs whitespace-nowrap sm:text-sm px-3 sm:px-4 py-2 rounded-lg font-semibold flex items-center justify-center gap-1 transition-colors duration-200 flex-1 md:flex-initial shadow-sm">
                            <span class="material-symbols-outlined text-base">inventory_2</span>
                            <span>Meus Produtos</span>
                        </a>
                    </div>
                </div>

                {{-- Abas de gestão --}}
                <div class="bg-white rounded-xl p-3 text-gray-800 shadow-inner">
                    <div class="flex items-center border-b border-gray-200 pb-2 mb-3 gap-4 text-xs sm:text-sm font-bold">
                        <button 
                            @click="abaAtiva = 'lotes'"
                            :class="abaAtiva === 'lotes' ? 'text-[#236350] border-b-2 border-[#236350] pb-1' : 'text-gray-400 hover:text-gray-600'"
                            class="transition-colors duration-150 flex items-center gap-1.5 cursor-pointer">
                            <span>Minhas Ofertas</span>
                            @if(isset($novasPropostas) && $novasPropostas > 0)
                                <span class="bg-amber-400 text-gray-900 text-[10px] px-1.5 py-0.2 rounded-full font-extrabold">
                                    {{ $novasPropostas }}
                                </span>
                            @endif
                        </button>

                        <button 
                            @click="abaAtiva = 'demandas'"
                            :class="abaAtiva === 'demandas' ? 'text-[#236350] border-b-2 border-[#236350] pb-1' : 'text-gray-400 hover:text-gray-600'"
                            class="transition-colors duration-150 cursor-pointer">
                            Demandas da Empresa
                        </button>
                    </div>

                    {{-- Conteúdo das abas --}}
                    <div x-show="abaAtiva === 'lotes'" class="space-y-2">
                        @forelse($ofertas->take(3) as $oferta)
                            @php
                                $temNovaProposta = isset($ofertasComNovasPropostas) && $ofertasComNovasPropostas->has($oferta->id);
                                $negociacao = $temNovaProposta ? $ofertasComNovasPropostas->get($oferta->id) : $oferta->negociacoes->sortByDesc('id')->first();
                            @endphp
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg border border-gray-100 text-md gap-2 transition-colors duration-150">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <img class="w-10 h-10 object-cover rounded-md shrink-0" 
                                         src="{{ $oferta->produto->imagem ? (str_contains($oferta->produto->imagem, 'assets/') ? asset($oferta->produto->imagem) : asset('storage/' . $oferta->produto->imagem)) : asset('assets/milho.png') }}" 
                                         alt="{{ $oferta->produto->nome ?? 'Produto' }}">
                                    
                                    <div class="truncate">
                                        <p class="font-bold text-gray-800 truncate">
                                            #{{ $oferta->id }} - {{ $oferta->produto->nome ?? '' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500">
                                            {{ number_format($oferta->quantidade, 0, ',', '.') }} {{ $oferta->unidade }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('negociacoes.propostas', $oferta->id) }}" 
                                    class="bg-slate-200 hover:bg-slate-300 font-bold px-2 py-1 rounded text-[11px] transition-colors">
                                        Negociação
                                    </a>
                                    <a href="{{ route('ofertas.show', $oferta->id) }}" 
                                    class="btn-copav hover:text-gray-900 px-2.5 py-1 rounded font-semibold text-[11px] transition-colors duration-150">
                                        Detalhes
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">Você ainda não possui ofertas ativas.</p>
                        @endforelse

                        {{-- Botão Ver Mais se houver mais de 3 ofertas --}}
                        @if($ofertas->count() > 3)
                            <div class="pt-2 text-center">
                                <a href="{{ route('ofertas.index') }}" 
                                   class="inline-block text-xs font-bold text-[#236350] hover:text-[#1b4d3e] hover:underline transition-colors py-1">
                                    Ver mais ofertas
                                </a>
                            </div>
                        @endif
                    </div>

                    <div x-show="abaAtiva === 'demandas'" x-cloak class="space-y-2">
                        @forelse($demandas->take(3) ?? [] as $demanda)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg border border-gray-100 text-xs gap-2 transition-colors duration-150">
                                <div class="truncate">
                                    <p class="font-bold text-gray-800 truncate">{{ $demanda->nome_produto }}</p>
                                    <p class="text-[11px] text-gray-500">{{ number_format($demanda->quantidade, 0, ',', '.') }} {{ $demanda->unidade }}</p>
                                </div>
                                <a href="{{ route('demandas.show', $demanda->id) }}" 
                                   class="btn-copav text-white px-2.5 py-1 rounded text-[11px] font-semibold shrink-0 transition-transform active:scale-95">
                                    Enviar Proposta
                                </a>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">Nenhuma demanda corporativa aberta.</p>
                        @endforelse
                    </div>
                </div>

            {{-- Se for cliente comum --}}
            @else
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div>
                        <h2 class="font-bold text-sm sm:text-base">Deseja vender no marketplace?</h2>
                        <p class="text-xs text-emerald-200">Torne-se um fornecedor e comece a cadastrar seus lotes.</p>
                    </div>
                    <a href="{{ route('fornecedores.create') }}" class="btn-copav text-xs px-4 py-2 rounded-lg font-bold shrink-0 transition-transform active:scale-95">
                        Virar Fornecedor
                    </a>
                </div>
            @endif
        </div>
    @endauth

    {{-- Usuário guest --}}
    @guest
        <div class="bg-[#123228] text-white p-4 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <div>
                <h2 class="font-bold text-sm sm:text-base">Quer comprar ou vender produtos agrícolas?</h2>
                <p class="text-xs text-emerald-200">Cadastre-se ou entre na sua conta para negociar lotes diretamente.</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('login') }}" class="btn-copav text-xs px-4 py-2 rounded-lg font-bold transition-transform active:scale-95">Entrar</a>
                <a href="{{ route('register') }}" class="bg-white/10 hover:bg-white/20 text-white text-xs px-4 py-2 rounded-lg font-bold transition-colors duration-150">Cadastrar</a>
            </div>
        </div>
    @endguest

    {{-- Vitrine de produtos --}}
    <div class="space-y-3">
        <h1 class="text-lg sm:text-xl font-bold text-gray-800">
            Produtos que você pode se interessar...
        </h1>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4">
            @forelse($produtos as $item)
                <a href="{{ route('ofertas.show', $item->id) }}" class="group block h-full">
                    <div class="bg-white rounded-xl shadow-sm group-hover:shadow-lg transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col h-full group-hover:-translate-y-0.5">
                        
                        {{-- Imagem --}}
                        <div class="w-full aspect-square bg-gray-100 overflow-hidden relative">
                            <img 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                src="{{ $item->produto->imagem 
                                    ? (str_contains($item->produto->imagem, 'assets/') ? asset($item->produto->imagem) : asset('storage/' . $item->produto->imagem)) 
                                    : asset('assets/milho.png') }}"
                                alt="{{ $item->produto->nome ?? 'Produto' }}">
                        </div>

                        {{-- Detalhes do card --}}
                        <div class="p-2.5 sm:p-3 flex flex-col flex-1 justify-between gap-1.5">
                            <div>
                                <h2 class="font-medium text-xs sm:text-sm text-gray-800 line-clamp-2 leading-tight group-hover:text-[#236350] transition-colors duration-150">
                                    {{ $item->produto->nome ?? 'Sem nome' }}
                                </h2>
                                @if($item->produto->descricao)
                                    <p class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">
                                        {{ $item->produto->descricao }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-1 border-t border-gray-100">
                                <span class="text-[10px] sm:text-xs text-gray-400 block uppercase font-medium">
                                    {{ $item->unidade }}
                                </span>
                                <span class="text-sm sm:text-base font-extrabold text-[#1B4D3E]">
                                    R$ {{ number_format($item->valor ?? 0, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                    </div>
                </a>
            @empty
                <p class="text-gray-500 col-span-full text-center py-8 text-xs sm:text-sm">
                    Nenhum produto cadastrado até o momento.
                </p>
            @endforelse
        </div>
    </div>

</div>

@endsection