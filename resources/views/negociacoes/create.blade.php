@extends('layouts.layout')

@section('title', 'Nova Negociação')

@section('conteudo')

{{-- Breadcrumbs --}}
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

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <a
                href="{{ route('negociacoes.index') }}"
                class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors"
            >
                Negociações
            </a>
        </li>

        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho --}}
    <div class="mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#1B4D3E] dark:text-emerald-400 text-3xl leading-none">handshake</span>
            <span>Nova Negociação</span>
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Selecione uma oferta disponível e defina o status inicial para abrir a negociação.
        </p>
    </div>

    {{-- Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 rounded-r-xl shadow-sm">
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

    {{-- Formulário --}}
    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] shadow-sm p-6 sm:p-8">
        <form action="{{ route('negociacoes.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="w-full">
                <label for="oferta_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                    Oferta <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <select
                    id="oferta_id"
                    name="oferta_id"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('oferta_id') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                >
                    <option value="">Selecione uma oferta</option>
                    @foreach($ofertas as $oferta)
                        <option
                            value="{{ $oferta->id }}"
                            {{ (string) old('oferta_id') === (string) $oferta->id ? 'selected' : '' }}
                        >
                            {{ $oferta->produto->nome ?? 'Produto' }} - {{ $oferta->fornecedor->nome ?? 'Fornecedor' }} - R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('oferta_id')
                    <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="w-full">
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                    Status inicial <span class="text-red-500 dark:text-red-400">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 border {{ $errors->has('status') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#1B4D3E] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                >
                    <option value="pendente" {{ old('status', 'pendente') === 'pendente' ? 'selected' : '' }}>
                        Pendente
                    </option>
                    <option value="em_negociacao" {{ old('status') === 'em_negociacao' ? 'selected' : '' }}>
                        Em negociação
                    </option>
                </select>
                @error('status')
                    <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-[#273449] flex items-center justify-end gap-3">
                <a
                    href="{{ route('negociacoes.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    Voltar
                </a>

                <button
                    type="submit"
                    class="btn-copav inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold text-white shadow-sm cursor-pointer transition-colors"
                >
                    <span class="material-symbols-outlined text-lg leading-none">add_circle</span>
                    <span>Criar negociação</span>
                </button>
            </div>
        </form>
    </div>

</div>

@endsection