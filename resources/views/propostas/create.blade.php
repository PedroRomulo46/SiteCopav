@extends('layouts.layout')
@section('title', 'Nova Proposta')

@section('conteudo')

{{-- Botão Voltar Superior --}}
<div class="mb-4">
    <a href="{{ route('negociacoes.show', $negociacao) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para os detalhes
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Nova Proposta
    </h1>

    {{-- Resumo da Oferta Atual --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Item em Negociação</span>
        <h2 class="text-xl font-bold text-gray-800 mb-3">
            {{ $negociacao->oferta->produto->nome }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div>
                <span class="text-gray-500 block">Fornecedor:</span>
                <span class="font-semibold text-gray-800">{{ $negociacao->oferta->fornecedor->nome }}</span>
            </div>

            <div>
                <span class="text-gray-500 block">Valor atual da oferta:</span>
                <span class="font-semibold text-gray-800">
                    R$ {{ number_format($negociacao->oferta->valor, 2, ',', '.') }}
                </span>
            </div>

            <div>
                <span class="text-gray-500 block">Quantidade disponível:</span>
                <span class="font-semibold text-gray-800">
                    @if(strtolower($negociacao->oferta->unidade) === 'saca')
                        {{ number_format($negociacao->oferta->quantidade, 0, ',', '.') }}
                    @else
                        {{ fmod($negociacao->oferta->quantidade, 1) == 0 ? number_format($negociacao->oferta->quantidade, 0, ',', '.') : number_format($negociacao->oferta->quantidade, 2, ',', '.') }}
                    @endif
                    {{ $negociacao->oferta->unidade }}
                </span>
            </div>
        </div>
    </div>

    {{-- Exibição de Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
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
            value="{{ $negociacao->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Valor da Proposta --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Valor da proposta (R$):
                </label>
                <input
                    type="number"
                    name="valor"
                    step="0.01"
                    min="0"
                    value="{{ old('valor') }}"
                    required
                    placeholder="0,00"
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>

            {{-- Quantidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Quantidade (em {{ $negociacao->oferta->unidade }}):
                </label>
                <input
                    type="number"
                    name="quantidade"
                    step="{{ strtolower($negociacao->oferta->unidade) === 'saca' ? '1' : '0.01' }}"
                    min="0"
                    value="{{ old('quantidade') }}"
                    placeholder="Informe a quantidade"
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]">
            </div>
        </div>

        {{-- Observação --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Observação:
            </label>
            <textarea
                name="observacao"
                rows="3"
                placeholder="Adicione observações ou detalhes adicionais à sua proposta..."
                class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-[#236350] focus:border-[#236350]"
            >{{ old('observacao') }}</textarea>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <a
                href="{{ route('negociacoes.show', $negociacao) }}"
                class="text-gray-600 hover:text-gray-900">
                Cancelar
            </a>

            <button
                type="submit"
                class="btn-copav text-white px-5 py-2 rounded-md transition-colors font-medium shadow-sm">
                Enviar proposta
            </button>
        </div>
    </form>
</div>

@endsection