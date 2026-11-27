@extends('layouts.layout')
@section('title', 'Editar fornecedor')

@section('conteudo')

@php
    $listaCategorias = collect($categorias ?? \App\Models\Categoria::orderBy('nome')->get());

    $categoriasSelecionadas = collect(old('categorias',
        method_exists($fornecedor, 'categorias') && $fornecedor->relationLoaded('categorias')
            ? $fornecedor->categorias->pluck('id')->all()
            : (method_exists($fornecedor, 'categorias') ? $fornecedor->categorias()->pluck('categorias.id')->all() : [])
    ))->map(fn ($id) => (int) $id)->all();
@endphp

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

        {{-- Item 2: Menu Fornecedor --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a
                href="{{ route('fornecedores.show', $fornecedor) }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors"
            >
                Menu fornecedor
            </a>
        </li>

        {{-- Item 3: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-4xl mx-auto my-6 p-6 bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] text-left">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Editar Cadastro de Fornecedor</h1>

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

    <form action="{{ route('fornecedores.update', $fornecedor) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Nome --}}
            <div class="md:col-span-2">
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nome:</label>
                <input
                    id="nome"
                    type="text"
                    name="nome"
                    value="{{ old('nome', $fornecedor->nome) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Documento --}}
            <div>
                <label for="documento" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Documento:</label>
                <input
                    id="documento"
                    type="text"
                    name="documento"
                    value="{{ old('documento', $fornecedor->documento) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('documento') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Telefone:</label>
                <input
                    id="telefone"
                    type="text"
                    name="telefone"
                    value="{{ old('telefone', $fornecedor->telefone) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('telefone') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descrição:</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    rows="3"
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">{{ old('descricao', $fornecedor->descricao) }}</textarea>
            </div>

            {{-- Endereço --}}
            <div class="md:col-span-2">
                <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Endereço:</label>
                <input
                    id="endereco"
                    type="text"
                    name="endereco"
                    value="{{ old('endereco', $fornecedor->endereco) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('endereco') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Cidade --}}
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Cidade:</label>
                <input
                    id="cidade"
                    type="text"
                    name="cidade"
                    value="{{ old('cidade', $fornecedor->cidade) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('cidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400">
            </div>

            {{-- Estado --}}
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado:</label>
                <input
                    id="estado"
                    type="text"
                    name="estado"
                    maxlength="2"
                    value="{{ old('estado', $fornecedor->estado) }}"
                    required
                    class="p-3 w-full rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('estado') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} shadow-sm outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400 uppercase">
            </div>

        </div>

        {{-- Categorias em foco (Tags) --}}
        @if($listaCategorias->isNotEmpty())
            <div class="pt-4 border-t border-gray-200 dark:border-[#273449]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    Categorias de atuação (tags):
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Selecione as categorias do site em que você foca suas vendas.
                </p>

                <div class="flex flex-wrap gap-2">
                    @foreach($listaCategorias as $categoria)
                        @php $marcada = in_array((int) $categoria->id, $categoriasSelecionadas, true); @endphp
                        <label class="cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="categorias[]"
                                value="{{ $categoria->id }}"
                                {{ $marcada ? 'checked' : '' }}
                                class="peer sr-only"
                            >
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-colors bg-gray-50 dark:bg-[#161f30] text-gray-700 dark:text-gray-300 border-gray-300 dark:border-[#273449] hover:border-[#236350] dark:hover:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/70 peer-checked:text-[#236350] dark:peer-checked:text-emerald-300 peer-checked:border-[#236350] dark:peer-checked:border-emerald-500">
                                <span class="material-symbols-outlined text-sm leading-none">label</span>
                                <span>{{ $categoria->nome }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-[#273449]">
            <a
                href="{{ route('fornecedores.show', $fornecedor) }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                Cancelar
            </a>
            <button
                type="submit"
                class="btn-copav inline-flex items-center justify-center gap-2 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition-colors cursor-pointer"
            >
                <span>Salvar alterações</span>
            </button>
        </div>
    </form>
</div>

@endsection