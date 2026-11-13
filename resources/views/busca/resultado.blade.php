@extends('layouts.layout')

@section('title', 'Resultados da Busca')

@section('conteudo')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Resultados para: "{{ $termo }}"</h1>
        <p class="text-gray-500 mt-1">Encontramos {{ $ofertas->count() }} resultado(s) correspondente(s).</p>
    </div>

    @if($ofertas->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($ofertas as $oferta)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="h-36 sm:h-48 bg-gray-100 overflow-hidden">
                        <img 
                            src="{{ $oferta->produto->imagem 
                                ? (str_contains($oferta->produto->imagem, 'assets/') ? asset($oferta->produto->imagem) : asset('storage/' . str_replace('public/', '', $oferta->produto->imagem))) 
                                : asset('assets/milho.png') }}" 
                            alt="{{ $oferta->produto->nome ?? 'Produto' }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <div class="p-3 sm:p-4 flex flex-col justify-between flex-1">
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-gray-900 mb-1 truncate">{{ $oferta->produto->nome ?? 'Lote' }}</h2>
                            <p class="text-[11px] sm:text-xs text-gray-500 mb-3 truncate">
                                Fornecedor: <strong class="text-gray-700">{{ $oferta->fornecedor->nome ?? 'Copav' }}</strong>
                            </p>
                        </div>

                        <div class="border-t border-gray-100 pt-3 mt-auto">
                            <div class="mb-3">
                                <span class="text-base sm:text-lg font-extrabold text-[#236350] block">
                                    R$ {{ number_format($oferta->valor, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] sm:text-xs text-gray-400">
                                    / {{ $oferta->unidade }}
                                </span>
                            </div>
                            <a href="{{ route('ofertas.show', $oferta->id) }}" class="w-full btn-copav py-2 rounded-xl text-xs sm:text-sm font-semibold text-center block shadow-sm">
                                Ver Oferta
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">search_off</span>
            <h2 class="text-lg font-bold text-gray-700">Nenhum resultado encontrado</h2>
            <p class="text-sm text-gray-500 mt-1">Tente pesquisar por outro termo ou produto.</p>
        </div>
    @endif
</div>
@endsection