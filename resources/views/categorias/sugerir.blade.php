@extends('layouts.layout')

@section('title', 'Sugerir Categoria')

@section('conteudo')

<div>
    {{-- breadcrumb --}}
    <nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li>
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
                >
                    Início
                </a>
            </li>

            @if(auth()->user()?->isAdmin())
                <li class="inline-flex items-center space-x-2">
                    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
                    <a
                        href="{{ route('categorias.index') }}"
                        class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
                    >
                        Categorias
                    </a>
                </li>
            @endif

            <li class="inline-flex items-center space-x-2">
                <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
                <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                    @yield('title')
                </span>
            </li>
        </ol>
    </nav>

    <div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">
        {{-- alerta de sucesso --}}
        @if(session('sucesso'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl leading-none">check_circle</span>
                    <span>{{ session('sucesso') }}</span>
                </div>
                <button
                    type="button"
                    onclick="this.parentElement.remove()"
                    class="inline-flex h-6 w-6 items-center justify-center rounded-full text-emerald-800 dark:text-emerald-200 hover:bg-emerald-100/70 dark:hover:bg-emerald-900/40 cursor-pointer transition-colors"
                    aria-label="Fechar alerta"
                >
                    ✕
                </button>
            </div>
        @endif

        {{-- cabeçalho da página --}}
        <div class="mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] dark:text-amber-400 text-3xl leading-none">lightbulb</span>
                <span>Sugerir Nova Categoria</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Não encontrou a categoria ideal para seu produto ou demanda? Envie uma sugestão para nossa equipe avaliar.
            </p>
        </div>

        {{-- formulário de sugestão --}}
        <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 sm:p-8">
            <form action="{{ route('categorias.sugerir.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- nome da categoria sugerida --}}
                <div>
                    <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Nome da categoria <span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <input
                        id="nome"
                        name="nome"
                        type="text"
                        value="{{ old('nome') }}"
                        placeholder="Ex: Sementes Orgânicas, Irrigação, Embalagens..."
                        required
                        autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    >
                    @error('nome')
                        <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- descrição / justificativa --}}
                <div>
                    <label for="descricao" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Descrição ou exemplos de produtos
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="4"
                        placeholder="Descreva brevemente quais tipos de produtos ou lotes entrariam nesta categoria..."
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('descricao') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    >{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- botões de ação --}}
                <div class="pt-3 border-t border-gray-100 dark:border-[#273449] flex flex-wrap items-center justify-end gap-3">
                    <a
                        href="{{ url()->previous() }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn-copav inline-flex items-center justify-center gap-2 px-6 py-2 rounded-lg text-sm font-medium shadow-sm cursor-pointer transition-colors"
                    >
                        <span class="material-symbols-outlined text-lg leading-none">send</span>
                        <span>Enviar Sugestão</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection