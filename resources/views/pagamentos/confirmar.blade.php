@extends('layouts.layout')

@section('title', 'Pagamento Confirmado - Simulador')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-7 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex flex-wrap items-center gap-2">
        {{-- Item 1: Inicio --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        {{-- Item 2: Compras --}}
        <li class="inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
                chevron_right
            </span>
            <a href="{{ route('negociacoes.compras') }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
                Minhas Compras
            </a>
        </li>

        {{-- Item 3: Verificar Pagamento --}}
        <li class="inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
                chevron_right
            </span>
            <a href="{{ route('pagamentos.show', $negociacao) }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
                Verificar Pagamento
            </a>
        </li>

        {{-- Item 4: Página Atual --}}
        <li class="inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
                chevron_right
            </span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                Pagamento Aprovado
            </span>
        </li>
    </ol>
</nav>

<div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
    <div class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 sm:p-8 text-center max-w-lg w-full">

        {{-- Ícone de Sucesso --}}
        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-950/60 text-[#236350] dark:text-emerald-400 border border-transparent dark:border-emerald-800/60 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl leading-none">check_circle</span>
        </div>

        <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60 text-xs font-bold tracking-wide uppercase mb-2">
            Simulação Concluída com Sucesso 🚀
        </span>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">Pagamento Aprovado!</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">A negociação foi paga com sucesso.</p>

        {{-- Resumo do valor pago --}}
        <div class="bg-gray-50 dark:bg-[#161f30] rounded-xl p-4 border border-gray-200/80 dark:border-[#273449] mb-6 text-left">
            <div class="flex justify-between items-center gap-2 mb-2 text-sm">
                <span class="text-gray-500 dark:text-gray-400">Produto:</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100 text-right">{{ $oferta->produto->nome ?? 'Produto' }}</span>
            </div>
            <div class="flex justify-between items-center gap-2 mb-2 text-sm">
                <span class="text-gray-500 dark:text-gray-400">Vendedor:</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100 text-right">{{ $oferta->fornecedor->nome ?? 'Fornecedor' }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-[#273449] my-2 pt-2 flex justify-between items-center gap-2">
                <span class="font-bold text-gray-700 dark:text-gray-200">Total Pago:</span>
                <span class="text-xl font-extrabold text-[#236350] dark:text-emerald-400">
                    R$ {{ number_format((float) ($valorTotal ?? 0), 2, ',', '.') }}
                </span>
            </div>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
            Este foi um pagamento simulado para validação do fluxo. Futuramente, esta etapa processará o Pix real via gateway.
        </p>

        {{-- Botões de Ação --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('chat.show', $negociacao) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#161f30] px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-lg leading-none">chat</span>
                <span>Ir para o Chat</span>
            </a>

            <a href="{{ route('negociacoes.compras') }}" class="btn-copav flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white transition-opacity hover:opacity-95">
                <span class="material-symbols-outlined text-lg leading-none">shopping_bag</span>
                <span>Minhas Compras</span>
            </a>
        </div>

    </div>
</div>

@endsection