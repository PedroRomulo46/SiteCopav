@extends('layouts.layout')

@section('title', $categoria->nome ?? 'Categoria')

@section('conteudo')

{{-- Breadcrumb --}}
<nav class="flex items-center text-sm mb-4 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li><a href="{{ route('home') }}" class="hover:text-[#236350]">Início</a></li>
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">@yield('title')</span>
        </li>
    </ol>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $categoria->nome }}</h1>
        <p class="text-gray-500 mt-1">Explore os produtos e ofertas disponíveis nesta categoria.</p>
    </div>

    {{-- Listagem de Ofertas/Produtos com espaçamento e layout idêntico à referência --}}
    @if(isset($categoria->produtos) && $categoria->produtos->count())
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($categoria->produtos as $produto)
                @foreach($produto->ofertas as $oferta)
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        
                        {{-- Imagem do Produto --}}
                        <div class="h-36 sm:h-48 bg-gray-100 overflow-hidden">
                            <img 
                                src="{{ $produto->imagem 
                                    ? (str_contains($produto->imagem, 'assets/') ? asset($produto->imagem) : asset('storage/' . str_replace('public/', '', $produto->imagem))) 
                                    : asset('assets/milho.png') }}" 
                                alt="{{ $produto->nome }}"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        {{-- Conteúdo do Card --}}
                        <div class="p-3 sm:p-4 flex flex-col justify-between flex-1">
                            <div>
                                <h2 class="text-sm sm:text-base font-bold text-gray-900 mb-1 truncate">{{ $produto->nome }}</h2>
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

                                {{-- Botão embaixo centralizado/largura total --}}
                                <a href="{{ route('ofertas.show', $oferta->id) }}" class="w-full btn-copav py-2 rounded-md text-xs sm:text-sm font-semibold text-center block shadow-sm">
                                    Ver Oferta
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">inventory_2</span>
            <h2 class="text-lg font-bold text-gray-700">Nenhum produto nesta categoria</h2>
            <p class="text-sm text-gray-500 mt-1">Ainda não há ofertas cadastradas para esta categoria.</p>
        </div>
    @endif

</div>
@endsection