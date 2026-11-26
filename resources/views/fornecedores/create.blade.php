@extends('layouts.layout')
@section('title', 'Cadastrar Fornecedor')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
            >
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] text-left">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Cadastrar-se como fornecedor</h1>

    {{-- Bloco para exibição de erros de validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-300 rounded-r-md">
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
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nome da empresa:</label>
                <input
                    id="nome"
                    type="text"
                    name="nome"
                    value="{{ old('nome') }}"
                    required
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Documento --}}
            <div>
                <label for="documento" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Documento (CPF/CNPJ):</label>
                <input
                    id="documento"
                    type="text"
                    name="documento"
                    value="{{ old('documento') }}"
                    required
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('documento') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Telefone:</label>
                <input
                    id="telefone"
                    type="text"
                    name="telefone"
                    value="{{ old('telefone') }}"
                    required
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('telefone') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Cidade --}}
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Cidade:</label>
                <input
                    id="cidade"
                    type="text"
                    name="cidade"
                    value="{{ old('cidade') }}"
                    required
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('cidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Estado --}}
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado (UF):</label>
                <input
                    id="estado"
                    type="text"
                    name="estado"
                    value="{{ old('estado') }}"
                    maxlength="2"
                    required
                    placeholder="Ex: CE"
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('estado') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400 uppercase">
            </div>

            {{-- Endereço --}}
            <div>
                <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Endereço:</label>
                <input
                    id="endereco"
                    type="text"
                    name="endereco"
                    value="{{ old('endereco') }}"
                    required
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('endereco') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descrição:</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    rows="3"
                    class="p-3 w-full rounded-md text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-1 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">{{ old('descricao') }}</textarea>
            </div>

        </div>

        {{-- Botões de Ação --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-[#273449]">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="btn-copav inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-medium text-sm shadow-md transition-all cursor-pointer border-0"
            >
                <span>Cadastrar</span>
            </button>
        </div>
    </form>
</div>

@endsection