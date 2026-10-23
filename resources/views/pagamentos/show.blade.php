
@extends('layouts.layout')

@section('title', 'Pagamento')

@section('conteudo')

@php
    $produto = $oferta->produto;

    $imagemProduto = $produto && $produto->imagem
        ? (
            str_contains($produto->imagem, 'assets/')
                ? asset($produto->imagem)
                : asset('storage/' . ltrim(
                    str_replace('public/', '', $produto->imagem),
                    '/'
                ))
        )
        : asset('assets/milho.png');
@endphp

{{-- Breadcrumb --}}
<nav class="flex flex-wrap items-center gap-2 text-sm mb-7"
     aria-label="Breadcrumb">

    <a href="{{ route('home') }}"
       class="text-gray-500 hover:text-[#236350]">
        Início
    </a>

    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>

    <a href="{{ route('negociacoes.compras') }}"
       class="text-gray-500 hover:text-[#236350]">
        Minhas Compras
    </a>

    <span class="material-symbols-outlined text-gray-400 text-lg">
        chevron_right
    </span>

    <span class="font-semibold text-gray-800">
        Pagamento
    </span>
</nav>

<div class="min-h-screen">
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        {{-- Cabeçalho --}}
        <header class="mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold
                       tracking-tight text-gray-900">
                Pagamento
            </h1>

            <p class="text-gray-500 mt-2 text-sm sm:text-base">
                Confira os dados da proposta aceita antes de continuar.
            </p>
        </header>

        {{-- Card do produto --}}
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 lg:p-6 mb-6">

            <div class="grid lg:grid-cols-2 md:grid-cols-[220px_minmax(0,1fr)] gap-5 lg:gap-7 items-start">

                {{-- Imagem do produto --}}
                <div class="relative w-full">
                    <img
                        src="{{ $imagemProduto }}"
                        alt="{{ $produto->nome ?? 'Produto' }}"
                        class="w-48 h-48 sm:h-44 lg:h-48 object-cover rounded-xl bg-gray-100"
                    >
                </div>

                {{-- Informações do produto --}}
                <div class="min-w-0">
                    <p class="flex items-center gap-2 text-xs font-bold
                            uppercase tracking-wider text-[#236350] mb-2">
                        <span class="material-symbols-outlined text-lg">
                            eco
                        </span>
                        Produto
                    </p>

                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900
                            break-words">
                        {{ $produto->nome ?? 'Produto' }}
                    </h2>

                    {{-- Vendedor --}}
                    <div class="flex items-center gap-3 mt-4">
                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-gray-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-700">
                                store
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">Vendedor</p>

                            <p class="font-bold text-gray-900 break-words">
                                {{ $oferta->fornecedor->nome ?? 'Fornecedor' }}
                            </p>
                        </div>
                    </div>

                    {{-- Negociação e status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">

                        <div class="rounded-xl border border-gray-200
                                    bg-gray-50 p-3 flex items-center gap-3">
                            <span class="material-symbols-outlined
                                        text-[#236350] text-xl shrink-0">
                                receipt_long
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs text-gray-500">Negociação</p>
                                <p class="font-semibold text-gray-900">
                                    #{{ $negociacao->id }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200
                                    bg-gray-50 p-3 flex items-center gap-3">
                            <span class="material-symbols-outlined
                                        text-[#236350] text-xl shrink-0">
                                verified
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs text-gray-500">Status</p>
                                <p class="font-semibold text-emerald-700">
                                    Proposta aceita
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        {{-- Resumo e painel lateral --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- Resumo do pedido --}}
            <section class="lg:col-span-2 bg-white rounded-2xl
                            border border-gray-200 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-5 sm:px-6 py-5
                            border-b border-gray-200">
                    <span class="material-symbols-outlined
                                 text-[#236350] text-2xl">
                        description
                    </span>

                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                        Resumo do pedido
                    </h2>
                </div>

                <div class="p-5 sm:p-6">

                    {{-- Valor unitário --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 shrink-0 rounded-full
                                    bg-emerald-50 flex items-center justify-center">
                            <span class="material-symbols-outlined
                                         text-[#236350] text-2xl">
                                payments
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">
                                Valor por unidade
                            </p>

                            <p class="flex flex-wrap items-baseline gap-2">
                                <span class="text-2xl font-bold text-[#236350]">
                                    R$ {{ number_format($valorUnitario, 2, ',', '.') }}
                                </span>

                                <span class="text-sm text-gray-500">
                                    / {{ $oferta->unidade }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Quantidade --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 shrink-0 rounded-full
                                    bg-emerald-50 flex items-center justify-center">
                            <span class="material-symbols-outlined
                                         text-[#236350] text-2xl">
                                scale
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">
                                Quantidade aceita
                            </p>

                            <p class="flex flex-wrap items-baseline gap-2">
                                <span class="text-2xl font-bold text-[#236350]">
                                    {{ number_format($quantidade, 2, ',', '.') }}
                                </span>

                                <span class="text-sm text-gray-500">
                                    {{ $oferta->unidade }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="border-t border-gray-200 pt-5">
                        <div class="rounded-xl bg-emerald-50
                                    border border-emerald-100 p-4 sm:p-5
                                    flex items-center gap-4">

                            <div class="w-12 h-12 shrink-0 rounded-full
                                        bg-white border border-emerald-100
                                        flex items-center justify-center">
                                <span class="material-symbols-outlined
                                             text-[#236350] text-2xl">
                                    account_balance_wallet
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-emerald-800">
                                    Total do pedido
                                </p>

                                <p class="text-2xl sm:text-3xl font-extrabold
                                          text-[#236350] break-words">
                                    R$ {{ number_format($valorTotal, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Forma de pagamento --}}
                    <div class="mt-5 rounded-xl border border-gray-200
                                bg-gray-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 rounded-full
                                        bg-emerald-100 flex items-center justify-center">
                                <span class="material-symbols-outlined
                                             text-[#236350] text-xl">
                                    credit_card
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-gray-900">
                                    Forma de pagamento
                                </h3>

                                <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                    As opções de pagamento estarão disponíveis
                                    após a integração de um meio de pagamento
                                    ao marketplace.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- Painel lateral --}}
            <aside class="bg-white rounded-2xl border border-gray-200
                          shadow-sm p-5">

                <div class="rounded-xl bg-emerald-50 border
                            border-emerald-100 p-4 mb-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined
                                     text-[#236350] text-2xl">
                            verified_user
                        </span>

                        <div>
                            <h3 class="font-bold text-gray-900">
                                Compra segura
                            </h3>

                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                Confira os dados da negociação antes de
                                prosseguir com a compra.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 py-4 border-b border-gray-100">
                    <span class="material-symbols-outlined text-[#236350] text-xl">
                        lock
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">
                            Proteção de dados
                        </h4>

                        <p class="text-sm text-gray-500 mt-1">
                            Mantenha seus dados de acesso protegidos.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 py-4 border-b border-gray-100">
                    <span class="material-symbols-outlined text-[#236350] text-xl">
                        handshake
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">
                            Negociação registrada
                        </h4>

                        <p class="text-sm text-gray-500 mt-1">
                            A proposta foi aceita pelo vendedor.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 pt-4">
                    <span class="material-symbols-outlined text-[#236350] text-xl">
                        support_agent
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">
                            Acompanhe sua compra
                        </h4>

                        <p class="text-sm text-gray-500 mt-1">
                            Consulte o chat para acompanhar a negociação.
                        </p>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Aviso --}}
        <div class="mt-6 flex items-start gap-3 rounded-xl
                    border border-amber-200 bg-amber-50 p-4">
            <span class="material-symbols-outlined
                         text-amber-600 text-xl shrink-0">
                info
            </span>

            <p class="text-sm text-amber-900 leading-relaxed">
                <strong>Prévia do pedido:</strong>
                nenhum pagamento foi realizado ou confirmado nesta tela.
                A cobrança real depende da integração de um meio de pagamento.
            </p>
        </div>

        {{-- Ações --}}
        <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3
                    sm:justify-between">

            <a
                href="{{ route('negociacoes.show', $negociacao) }}"
                class="flex items-center justify-center gap-2 rounded-xl
                       border border-gray-300 bg-white px-5 py-3
                       text-sm font-semibold text-gray-700
                       hover:bg-gray-50 transition-colors"
            >
                <span class="material-symbols-outlined text-lg">
                    arrow_back
                </span>
                Voltar ao chat
            </a>

            <a
                href="{{ route('negociacoes.compras') }}"
                class="flex items-center justify-center gap-2 rounded-xl
                       bg-[#236350] px-5 py-3 text-sm font-semibold
                       text-white hover:bg-[#194b3c] transition-colors"
            >
                <span class="material-symbols-outlined text-lg">
                    shopping_bag
                </span>
                Minhas compras
            </a>

        </div>

    </main>
</div>

@endsection
