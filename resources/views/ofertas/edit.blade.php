@extends('layouts.layout')

@section('title', 'Editar Oferta')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
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

<div class="max-w-4xl mx-auto text-left">

    {{-- Cabeçalho --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            Editar oferta
        </h1>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Atualize as informações da oferta.
        </p>
    </div>

    {{-- Mensagens de erro --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-300 rounded-r-md">

            <p class="font-semibold mb-2">
                Corrija os seguintes erros:
            </p>

            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-6">

        <form
            id="form-editar-oferta"
            action="{{ route('ofertas.update', $oferta) }}"
            method="POST"
            class="space-y-6"
        >

            @csrf
            @method('PUT')

            {{-- Produto --}}
            <div>
                <label
                    for="produto_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                >
                    Produto
                </label>

                <select
                    name="produto_id"
                    id="produto_id"
                    required
                    class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('produto_id') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >
                    <option value="">
                        Selecione um produto
                    </option>

                    @foreach($produtos as $produto)
                        <option
                            value="{{ $produto->id }}"
                            {{ (string) old('produto_id', $oferta->produto_id) === (string) $produto->id ? 'selected' : '' }}
                        >
                            {{ $produto->nome }}

                            @if($produto->categoria)
                                — {{ $produto->categoria->nome }}
                            @endif
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Quantidade e unidade --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label
                        for="quantidade"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                    >
                        Quantidade
                    </label>

                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade"
                        value="{{ old('quantidade', $oferta->quantidade) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('quantidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                    >
                </div>

                <div>
                    <label
                        for="unidade"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Unidade
                    </label>

                    <input
                        type="text"
                        name="unidade"
                        id="unidade"
                        value="{{ old('unidade', $oferta->unidade) }}"
                        maxlength="50"
                        required
                        placeholder="Ex.: kg, saca, tonelada"
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('unidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                    >
                </div>

            </div>

            {{-- Valor --}}
            <div>
                <label
                    for="valor"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Valor
                </label>

                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400 pointer-events-none">
                        R$
                    </span>

                    <input
                        type="number"
                        name="valor"
                        id="valor"
                        value="{{ old('valor', $oferta->valor) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full rounded-lg pl-10 pr-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('valor') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                    >
                </div>
            </div>

            {{-- Localização --}}
            <div>
                <label
                    for="localizacao"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                >
                    Localização
                </label>

                <input
                    type="text"
                    name="localizacao"
                    id="localizacao"
                    value="{{ old('localizacao', $oferta->localizacao) }}"
                    maxlength="255"
                    placeholder="Ex.: Icapuí - CE"
                    class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('localizacao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label
                        for="data_inicio"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2"
                    >
                        Data de início
                    </label>

                    <input
                        type="date"
                        name="data_inicio"
                        id="data_inicio"
                        value="{{ old('data_inicio', $oferta->data_inicio ? \Carbon\Carbon::parse($oferta->data_inicio)->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('data_inicio') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                    >
                </div>

                <div>
                    <label
                        for="data_validade"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Data de validade
                    </label>

                    <input
                        type="date"
                        name="data_validade"
                        id="data_validade"
                        value="{{ old('data_validade', $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('data_validade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                    >
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    required
                    class="w-full rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('status') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:ring-2 focus:ring-[#236350] focus:border-[#236350] dark:focus:border-emerald-400"
                >
                    <option
                        value="rascunho"
                        {{ old('status', $oferta->status) === 'rascunho' ? 'selected' : '' }}>
                        Rascunho
                    </option>
                    <option
                        value="publicada"
                        {{ old('status', $oferta->status) === 'publicada' ? 'selected' : '' }}>
                        Publicada
                    </option>
                    <option
                        value="encerrada"
                        {{ old('status', $oferta->status) === 'encerrada' ? 'selected' : '' }}>
                        Encerrada
                    </option>
                    <option
                        value="cancelada"
                        {{ old('status', $oferta->status) === 'cancelada' ? 'selected' : '' }}>
                        Cancelada
                    </option>
                </select>
            </div>

        </form>

        {{-- Botões --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-[#273449]">

            {{-- Excluir à esquerda --}}
            <form
                action="{{ route('ofertas.destroy', $oferta) }}"
                method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir esta oferta?');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors cursor-pointer"
                >
                    <span class="material-symbols-outlined text-lg leading-none">
                        delete
                    </span>
                    <span>Excluir oferta</span>
                </button>
            </form>

            {{-- Cancelar e salvar à direita --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a
                    href="{{ route('ofertas.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 gap-1.5 border border-gray-300 dark:border-[#273449] text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-lg leading-none">
                        block
                    </span>
                    <span>Cancelar</span>
                </a>

                <button
                    type="submit"
                    form="form-editar-oferta"
                    class="btn-copav inline-flex items-center justify-center gap-2 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-lg leading-none">
                        upload_file
                    </span>
                    <span>Salvar</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection