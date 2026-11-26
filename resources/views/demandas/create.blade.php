@extends('layouts.layout')
@section('title', 'Cadastrar Demanda')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        <!-- Item 1: Início -->
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        <!-- Item 2: Demandas -->
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a href="{{ route('demandas.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Demandas
            </a>
        </li>

        <!-- Item 3: Página Atual -->
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">@yield('title')</span>
        </li>

    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho da Página --}}
    <div class="flex flex-row items-center justify-between gap-4 mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
        <div class="text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">add_shopping_cart</span>
                <span>Cadastrar Demanda</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Informe o produto ou insumo que sua empresa está procurando no mercado
            </p>
        </div>
    </div>

    {{-- Exibição de Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 rounded-r-xl text-left shadow-sm">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-300 font-semibold mb-1">
                <span class="material-symbols-outlined text-xl leading-none">error</span>
                <span>Por favor, corrija os erros abaixo:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cartão Principal do Formulário --}}
    <div class="max-w-6xl mx-auto bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 sm:p-8">
        <form action="{{ route('demandas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Categoria --}}
                <div class="w-full">
                    <label for="categoria_id" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">category</span>
                        <span>Categoria <span class="text-red-500 dark:text-red-400">*</span></span>
                    </label>
                    <select
                        id="categoria_id"
                        name="categoria_id"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('categoria_id') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        required
                    >
                        <option value="" disabled selected>Selecione uma categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Produto que Procura --}}
                <div class="w-full">
                    <label for="nome_produto" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">inventory_2</span>
                        <span>Produto que procura <span class="text-red-500 dark:text-red-400">*</span></span>
                    </label>
                    <input
                        id="nome_produto"
                        type="text"
                        name="nome_produto"
                        value="{{ old('nome_produto') }}"
                        placeholder="Ex: Milho em grão"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome_produto') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        required
                    />
                </div>
            </div>

            {{-- Imagem da Demanda --}}
            <div class="w-full">
                <label for="imagem" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                    <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">image</span>
                    <span>Imagem da demanda</span>
                </label>

                <input
                    id="imagem"
                    type="file"
                    name="imagem"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-700 dark:text-gray-200 border {{ $errors->has('imagem') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 dark:file:bg-emerald-950/60 file:text-[#1B4D3E] dark:file:text-emerald-300 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-900/50 outline-none transition cursor-pointer"
                >

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    JPG, PNG ou WEBP. Máximo de 2 MB.
                </p>
            </div>

            {{-- Descrição --}}
            <div class="w-full">
                <label for="descricao" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                    <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">description</span>
                    <span>Descrição detalhada</span>
                </label>
                <textarea
                    id="descricao"
                    name="descricao"
                    rows="4"
                    placeholder="Descreva especificações, padrão de qualidade ou exigências da sua empresa..."
                    class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 resize-y"
                >{{ old('descricao') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Quantidade --}}
                <div class="w-full">
                    <label for="quantidade" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">format_list_numbered</span>
                        <span>Quantidade necessária <span class="text-red-500 dark:text-red-400">*</span></span>
                    </label>
                    <input
                        id="quantidade"
                        type="number"
                        name="quantidade"
                        step="0.01"
                        min="0.01"
                        value="{{ old('quantidade') }}"
                        placeholder="Ex: 500.00"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('quantidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        required
                    />
                </div>

                {{-- Unidade --}}
                <div class="w-full">
                    <label for="unidade" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">straighten</span>
                        <span>Unidade de Medida <span class="text-red-500 dark:text-red-400">*</span></span>
                    </label>
                    <input
                        id="unidade"
                        type="text"
                        name="unidade"
                        value="{{ old('unidade') }}"
                        placeholder="Ex: kg, sacas, toneladas"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('unidade') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        required
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Valor Máximo --}}
                <div class="w-full">
                    <label for="valor_maximo" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">payments</span>
                        <span>Valor máx. a pagar (R$)</span>
                    </label>
                    <input
                        id="valor_maximo"
                        type="number"
                        name="valor_maximo"
                        step="0.01"
                        min="0"
                        value="{{ old('valor_maximo') }}"
                        placeholder="0,00"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('valor_maximo') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    />
                </div>

                {{-- Localização --}}
                <div class="w-full">
                    <label for="localizacao" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">location_on</span>
                        <span>Localização / Entrega</span>
                    </label>
                    <input
                        id="localizacao"
                        type="text"
                        name="localizacao"
                        value="{{ old('localizacao', '') }}"
                        placeholder="Ex: Icapuí - CE"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('localizacao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    />
                </div>

                {{-- Data Limite --}}
                <div class="w-full">
                    <label for="data_limite" class="flex items-center gap-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-lg leading-none">event</span>
                        <span>Data Limite de Cotação</span>
                    </label>
                    <input
                        id="data_limite"
                        type="date"
                        name="data_limite"
                        value="{{ old('data_limite') }}"
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-gray-50 dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('data_limite') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    />
                </div>
            </div>

            {{-- Rodapé / Botões de Ação --}}
            <div class="pt-6 border-t border-gray-100 dark:border-[#273449] flex items-center justify-end gap-3">
                <a
                    href="{{ route('demandas.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold text-white shadow-sm cursor-pointer transition-colors"
                >
                    <span class="material-symbols-outlined text-xl leading-none">check_circle</span>
                    <span>Cadastrar Demanda</span>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection