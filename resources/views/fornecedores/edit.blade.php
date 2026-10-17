@extends('layouts.layout')
@section('title', 'Editar fornecedor')

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

        {{-- Item 2: Menu Fornecedor --}}
        <li>
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{route('fornecedores.show', $fornecedor)}}">
                Menu fornecedor
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
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Editar Cadastro de Fornecedor</h1>

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

    <form action="{{ route('fornecedores.update', $fornecedor) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nome --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome:</label>
                <input 
                    type="text" 
                    name="nome" 
                    value="{{ old('nome', $fornecedor->nome) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Documento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento:</label>
                <input 
                    type="text" 
                    name="documento" 
                    value="{{ old('documento', $fornecedor->documento) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Telefone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone:</label>
                <input 
                    type="text" 
                    name="telefone" 
                    value="{{ old('telefone', $fornecedor->telefone) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição:</label>
                <textarea 
                    name="descricao" 
                    rows="3" 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('descricao', $fornecedor->descricao) }}</textarea>
            </div>

            {{-- Endereço --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço:</label>
                <input 
                    type="text" 
                    name="endereco" 
                    value="{{ old('endereco', $fornecedor->endereco) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Cidade --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade:</label>
                <input 
                    type="text" 
                    name="cidade" 
                    value="{{ old('cidade', $fornecedor->cidade) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                <input 
                    type="text" 
                    name="estado" 
                    maxlength="2" 
                    value="{{ old('estado', $fornecedor->estado) }}" 
                    required 
                    class="p-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase">
            </div>

        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
            <a href="{{ route('fornecedores.show', $fornecedor) }}" class="text-gray-600 hover:text-gray-900">
                Cancelar
            </a>
            <button type="submit" class="btn-copav text-white px-5 py-2 rounded-md transition-colors">
                Salvar alterações
            </button>
        </div>
    </form>
</div>

@endsection