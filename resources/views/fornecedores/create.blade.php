@extends('layouts.layout')
@section('title', 'Menu fornecedor')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li>
            <a href="{{route('home')}}">
                Início
            </a>
        </li>

        {{-- Item 2: Pégina Atual --}}
        <li class="flex items-center space-x-2 hover:cursor-pointer">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Cadastrar-se como fornecedor</h1>

    {{-- Bloco para exibição de erros de validação --}}
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

    <form action="{{ route('fornecedores.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nome da empresa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da empresa:</label>
                <input
                    type="text"
                    name="nome"
                    value="{{ old('nome') }}"
                    required
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Documento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento (CPF/CNPJ):</label>
                <input
                    type="text"
                    name="documento"
                    value="{{ old('documento') }}"
                    required
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Telefone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone:</label>
                <input
                    type="text"
                    name="telefone"
                    value="{{ old('telefone') }}"
                    required
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Cidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade:</label>
                <input
                    type="text"
                    name="cidade"
                    value="{{ old('cidade') }}"
                    required
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado (UF):</label>
                <input
                    type="text"
                    name="estado"
                    value="{{ old('estado') }}"
                    maxlength="2"
                    required
                    placeholder="Ex: CE"
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase">
            </div>

            {{-- Endereço --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço:</label>
                <input
                    type="text"
                    name="endereco"
                    value="{{ old('endereco') }}"
                    required
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição:</label>
                <textarea 
                    name="descricao" 
                    rows="3" 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('descricao') }}</textarea>
            </div>

        </div>

        {{-- Botões de Ação --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                Cancelar
            </a>
            
            <button type="submit" class="btn-copav !px-4 !py-2 rounded-lg font-semibold text-sm shadow-md hover:opacity-90 transition-all cursor-pointer border-0 flex items-center gap-2">
                <span class="material-symbols-outlined">globe_location_pin</span>
                Se Cadastrar
            </button>
        </div>
    </form>
</div>

@endsection