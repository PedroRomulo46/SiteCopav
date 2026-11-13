@extends('layouts.layout')

@section('title', 'Pagamento Confirmado - Simulador')

@section('conteudo')

{{-- Breadcrumb --}}
<nav class="flex flex-wrap items-center gap-2 text-sm mb-7" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#236350]">Início</a>
    <span class="material-symbols-outlined text-gray-400 text-lg">chevron_right</span>
    <a href="{{ route('negociacoes.compras') }}" class="text-gray-500 hover:text-[#236350]">Minhas Compras</a>
    <span class="material-symbols-outlined text-gray-400 text-lg">chevron_right</span>
    <a href="{{ route('pagamentos.show', $negociacao) }}" class="text-gray-500 hover:text-[#236350]">Verificar Pagamento</a>
    <span class="material-symbols-outlined text-gray-400 text-lg">chevron_right</span>
    <span class="font-semibold text-gray-800">Pagamento Aprovado</span>
</nav>

<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 text-center max-w-lg w-full">
        
        {{-- Ícone de Sucesso --}}
        <div class="w-16 h-16 bg-emerald-100 text-[#236350] rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">check_circle</span>
        </div>

        <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold tracking-wide uppercase mb-2">
            Simulação Concluída com Sucesso 🚀
        </span>

        <h2 class="text-2xl font-bold text-gray-900 mb-1">Pagamento Aprovado!</h2>
        <p class="text-sm text-gray-500 mb-6">A negociação foi paga com sucesso.</p>

        {{-- Resumo do valor pago --}}
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6 text-left">
            <div class="flex justify-between items-center mb-2 text-sm">
                <span class="text-gray-500">Produto:</span>
                <span class="font-semibold text-gray-900">{{ $oferta->produto->nome ?? 'Produto' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2 text-sm">
                <span class="text-gray-500">Vendedor:</span>
                <span class="font-semibold text-gray-900">{{ $oferta->fornecedor->nome ?? 'Fornecedor' }}</span>
            </div>
            <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                <span class="font-bold text-gray-700">Total Pago:</span>
                <span class="text-xl font-extrabold text-[#236350]">R$ {{ number_format($valorTotal, 2, ',', '.') }}</span>
            </div>
        </div>

        <p class="text-xs text-gray-500 mb-6 leading-relaxed">
            Este foi um pagamento simulado para validação do fluxo. Futuramente, esta etapa processará o Pix real via gateway.
        </p>

        {{-- Botões de Ação --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('chat.show', $negociacao) }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-lg">chat</span>
                Ir para o Chat
            </a>

            <a href="{{ route('negociacoes.compras') }}" class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-[#236350] px-4 py-3 text-sm font-semibold text-white hover:bg-[#194b3c] transition-colors">
                <span class="material-symbols-outlined text-lg">shopping_bag</span>
                Minhas Compras
            </a>
        </div>

    </div>
</div>

@endsection