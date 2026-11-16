@extends('layouts.layout')

@section('title', 'Acompanhar Encomenda')

@section('conteudo')

{{-- Breadcrumb --}}
<nav class="flex flex-wrap items-center gap-2 text-sm mb-7" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#236350]">
        Início
    </a>
    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>
    <a href="{{ route('negociacoes.compras') }}" class="text-gray-500 hover:text-[#236350]">
        Minhas Compras
    </a>
    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>
    <span class="font-semibold text-gray-800">
        Acompanhar Encomenda
    </span>
</nav>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detalhes da Encomenda</h1>
        <p class="text-gray-500 mt-1">Acompanhe o status de separação, envio e entrega do seu pedido (#{{ $negociacao->id }}).</p>
    </div>

    {{-- Card do Produto / Encomenda --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between border-b border-gray-100 pb-6 mb-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold tracking-wide uppercase mb-2">
                    Compra Aprovada
                </span>
                <h2 class="text-xl font-bold text-gray-900">{{ $produto->nome ?? 'Produto' }}</h2>
                <p class="text-sm text-gray-500 mt-1">Fornecedor: <strong class="text-gray-700">{{ $oferta->fornecedor->nome ?? 'Copav' }}</strong></p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-xs text-gray-400 block">Negociação</span>
                <span class="text-lg font-bold text-gray-800">#{{ $negociacao->id }}</span>
            </div>
        </div>

        {{-- Status da Entrega (Timeline Simples) --}}
        <div class="space-y-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Status do Pedido</h3>
            
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">check</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Pagamento Aprovado</h4>
                    <p class="text-xs text-gray-500">O pagamento foi processado e confirmado com sucesso.</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Em separação pelo Fornecedor</h4>
                    <p class="text-xs text-gray-500">O fornecedor está preparando e embalando o seu lote.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 opacity-50">
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">local_shipping</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Saiu para Entrega / Retirada</h4>
                    <p class="text-xs text-gray-500">Aguardando despacho logístico.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Botões de Ação --}}
    <div class="flex gap-3">
        <a href="{{ route('chat.show', $negociacao) }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-lg">chat</span>
            Falar com o Vendedor
        </a>
        <a href="{{ route('negociacoes.compras') }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-[#236350] px-4 py-3 text-sm font-semibold text-white hover:bg-[#194b3c] transition-colors">
            <span class="material-symbols-outlined text-lg">shopping_bag</span>
            Minhas Compras
        </a>
    </div>
</div>

@endsection