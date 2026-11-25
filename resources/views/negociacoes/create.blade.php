@extends('layouts.layout')

@section('title', 'Nova Negociação')

@section('conteudo')

{{-- Breadcrumbs --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                Início
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <a href="{{ route('negociacoes.index') }}" class="hover:text-[#1B4D3E]">
                Negociações
            </a>
        </li>

        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-3xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho --}}
    <div class="mb-6 border-b border-gray-200 pb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">handshake</span>
            Nova Negociação
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Selecione uma oferta disponível e defina o status inicial para abrir a negociação.
        </p>
    </div>

    {{-- Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
            <div class="flex items-center gap-2 text-red-700 font-semibold mb-1">
                <span class="material-symbols-outlined text-xl">error</span>
                <span>Por favor, corrija os erros abaixo:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form action="{{ route('negociacoes.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="form-control w-full">
                <label for="oferta_id" class="label py-1">
                    <span class="label-text font-medium text-gray-700">
                        Oferta <span class="text-red-500">*</span>
                    </span>
                </label>
                <select
                    id="oferta_id"
                    name="oferta_id"
                    required
                    class="select select-bordered w-full focus:border-[#1B4D3E] focus:outline-none @error('oferta_id') border-red-500 @enderror"
                >
                    <option value="">Selecione uma oferta</option>
                    @foreach($ofertas as $oferta)
                        <option
                            value="{{ $oferta->id }}"
                            {{ (string) old('oferta_id') === (string) $oferta->id ? 'selected' : '' }}
                        >
                            {{ $oferta->produto->nome ?? 'Produto' }}
                            - {{ $oferta->fornecedor->nome ?? 'Fornecedor' }}
                            - R$ {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('oferta_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-control w-full">
                <label for="status" class="label py-1">
                    <span class="label-text font-medium text-gray-700">
                        Status inicial <span class="text-red-500">*</span>
                    </span>
                </label>
                <select
                    id="status"
                    name="status"
                    required
                    class="select select-bordered w-full focus:border-[#1B4D3E] focus:outline-none @error('status') border-red-500 @enderror"
                >
                    <option value="pendente" {{ old('status', 'pendente') === 'pendente' ? 'selected' : '' }}>
                        Pendente
                    </option>
                    <option value="em_negociacao" {{ old('status') === 'em_negociacao' ? 'selected' : '' }}>
                        Em negociação
                    </option>
                </select>
                @error('status')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('negociacoes.index') }}" class="btn btn-ghost text-gray-600 hover:bg-gray-100">
                    Voltar
                </a>

                <button type="submit" class="btn btn-copav text-white border-none gap-2 px-6 shadow-sm font-medium">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Criar negociação
                </button>
            </div>
        </form>
    </div>

</div>

@endsection