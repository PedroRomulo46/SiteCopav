@extends('layouts.layout')
@section('title', 'Criar novo lote')

@section('conteudo')

<div class="text-gray-500 mx-4 mt-2">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 hover:text-gray-700">
        <span class="material-symbols-outlined">arrow_back</span>
        Voltar para os produtos
    </a>
</div>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Cadastrar Nova Oferta</h1>

    {{-- Bloco correto para exibição de erros de validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
            <p class="font-bold">Atenção! Corrija os erros abaixo:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ofertas.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Produto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produto:</label>
                <select name="produto_id" required class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione o produto</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}" {{ old('produto_id') == $produto->id ? 'selected' : '' }}>
                            {{ $produto->nome }} - {{ $produto->categoria->nome ?? 'Sem Categoria' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade:</label>
                <input
                    type="number" 
                    name="quantidade" 
                    step="1" 
                    min="0" 
                    value="{{ old('quantidade') }}" 
                    required 
                    class="w-full p-3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor (R$):</label>
                <input 
                    type="number" 
                    name="valor" 
                    step="0.01" 
                    min="0" 
                    value="{{ old('valor') }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Unidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidade:</label>
                <select name="unidade" required class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione a unidade</option>
                    <option value="kg" {{ old('unidade') == 'kg' ? 'selected' : '' }}>Kg</option>
                    <option value="saca" {{ old('unidade') == 'saca' ? 'selected' : '' }}>Saca</option>
                </select>
            </div>

            {{-- Localização --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização:</label>
                <input 
                    type="text" 
                    name="localizacao" 
                    value="{{ old('localizacao') }}" 
                    placeholder="Ex: Icapuí - CE" 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Data de Início --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de início:</label>
                <input 
                    type="date" 
                    name="data_inicio" 
                    value="{{ old('data_inicio') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Data de Validade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de validade:</label>
                <input 
                    type="date" 
                    name="data_validade" 
                    value="{{ old('data_validade') }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Status --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                <select name="status" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="rascunho" {{ old('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                    <option value="publicada" {{ old('status', 'publicada') == 'publicada' ? 'selected' : '' }}>Publicada</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('ofertas.index') }}" class="text-gray-600 hover:text-gray-900">
                Cancelar
            </a>
            <button type="submit" class="bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2 rounded-md transition-colors">
                Cadastrar oferta
            </button>
        </div>
    </form>
</div>

@endsection