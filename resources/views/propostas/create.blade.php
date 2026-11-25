@extends('layouts.layout')
@section('title', 'Nova Proposta')

@section('conteudo')

@php
    $oferta = $negociacao?->oferta;
    $produto = $oferta?->produto;
    $fornecedor = $oferta?->fornecedor;
    $unidadeOferta = $oferta?->unidade ?? 'unidade';
    $quantidadeDisponivel = (float) ($oferta?->quantidade ?? 0);
    $valorAtualOferta = (float) ($oferta?->valor ?? 0);
@endphp

{{-- Breadcrumbs --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex flex-wrap items-center gap-2">

        {{-- Item 1: Início --}}
        <li class="flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#236350] transition-colors">
                Início
            </a>
        </li>

        {{-- Item 2: Nome do Produto / Oferta --}}
        @if($oferta && $produto)
            <li class="flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                <a href="{{ route('ofertas.show', $oferta) }}" class="hover:text-[#236350] transition-colors">
                    {{ $produto->nome ?? 'Oferta' }}
                </a>
            </li>
        @endif

        {{-- Item 3: Painel de Negociação --}}
        <li class="flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{ route('negociacoes.show', $negociacao) }}" class="hover:text-[#236350] transition-colors">
                Painel de Negociação
            </a>
        </li>

        {{-- Item 4: Página Atual --}}
        <li class="flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                Nova proposta
            </span>
        </li>

    </ol>
</nav>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md border border-gray-100 text-left">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Nova Proposta
    </h1>

    {{-- Resumo da Oferta Atual --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Item em Negociação</span>
        <h2 class="text-xl font-bold text-gray-800 mb-3">
            {{ $produto?->nome ?? 'Produto não informado' }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div>
                <span class="text-gray-500 block">Fornecedor:</span>
                <span class="font-semibold text-gray-800">{{ $fornecedor?->nome ?? 'Fornecedor não informado' }}</span>
            </div>

            <div>
                <span class="text-gray-500 block">Valor atual da oferta:</span>
                <span class="font-semibold text-gray-800">
                    R$ {{ number_format($valorAtualOferta, 2, ',', '.') }}
                </span>
            </div>

            <div>
                <span class="text-gray-500 block">Quantidade disponível:</span>
                <span class="font-semibold text-gray-800">
                    @if(strtolower((string) $unidadeOferta) === 'saca')
                        {{ number_format($quantidadeDisponivel, 0, ',', '.') }}
                    @else
                        {{ fmod($quantidadeDisponivel, 1) == 0 ? number_format($quantidadeDisponivel, 0, ',', '.') : number_format($quantidadeDisponivel, 2, ',', '.') }}
                    @endif
                    {{ $unidadeOferta }}
                </span>
            </div>
        </div>
    </div>

    {{-- Exibição de Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-md">
            <p class="font-bold">
                Atenção! Corrija os erros abaixo:
            </p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário de Proposta --}}
    <form action="{{ route('propostas.store') }}" method="POST" class="space-y-4">
        @csrf

        <input
            type="hidden"
            name="negociacao_id"
            value="{{ $negociacao?->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Valor da Proposta --}}
            <div>
                <label for="valor_proposta" class="block text-sm font-medium text-gray-700 mb-1">
                    Valor da proposta (R$):
                </label>
                <input
                    type="number"
                    name="valor"
                    id="valor_proposta"
                    step="0.01"
                    min="0"
                    value="{{ old('valor') }}"
                    required
                    placeholder="0,00"
                    class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Quantidade --}}
            <div>
                <label for="quantidade_proposta" class="block text-sm font-medium text-gray-700 mb-1">
                    Quantidade (em {{ $unidadeOferta }}):
                </label>
                <input
                    type="number"
                    name="quantidade"
                    id="quantidade_proposta"
                    step="{{ strtolower((string) $unidadeOferta) === 'saca' ? '1' : '0.01' }}"
                    min="0"
                    value="{{ old('quantidade') }}"
                    placeholder="Informe a quantidade"
                    class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>
        </div>

        {{-- Valor a Pagar --}}
        <div>
            <label for="valor_a_pagar" class="block text-sm font-medium text-gray-700 mb-1">
                Valor a pagar (R$):
            </label>
            <input
                type="text"
                id="valor_a_pagar"
                readonly
                value="R$ 0,00"
                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-md shadow-sm text-gray-700 font-semibold cursor-not-allowed">
        </div>

        {{-- Observação --}}
        <div>
            <label for="observacao_proposta" class="block text-sm font-medium text-gray-700 mb-1">
                Observação:
            </label>
            <textarea
                name="observacao"
                id="observacao_proposta"
                rows="3"
                placeholder="Adicione observações ou detalhes adicionais à sua proposta..."
                class="w-full p-3 border border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
            >{{ old('observacao') }}</textarea>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <a
                href="{{ route('negociacoes.show', $negociacao) }}"
                class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                Cancelar
            </a>

            <button
                type="submit"
                class="btn-copav text-white px-5 py-2.5 rounded-md transition-opacity hover:opacity-90 font-medium text-sm shadow-sm cursor-pointer">
                Enviar proposta
            </button>
        </div>
    </form>
</div>

<script>
    const inputValorProposta = document.getElementById('valor_proposta');
    const inputQuantidadeProposta = document.getElementById('quantidade_proposta');
    const inputValorAPagar = document.getElementById('valor_a_pagar');

    function atualizarValorAPagar() {
        const valor = parseFloat(inputValorProposta?.value) || 0;
        const quantidade = parseFloat(inputQuantidadeProposta?.value) || 0;
        const total = valor * quantidade;

        if (inputValorAPagar) {
            inputValorAPagar.value = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }
    }

    inputValorProposta?.addEventListener('input', atualizarValorAPagar);
    inputQuantidadeProposta?.addEventListener('input', atualizarValorAPagar);

    atualizarValorAPagar();
</script>

@endsection