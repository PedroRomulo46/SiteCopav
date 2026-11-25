@extends('layouts.layout')
@section('title', 'Fornecedores')

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
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 text-left">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#1B4D3E] text-3xl">storefront</span>
                Lista de Fornecedores
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Consulte os fornecedores cadastrados no marketplace
            </p>
        </div>

        @auth
            @php
                $fornecedorUsuario = auth()->user()?->fornecedor;
            @endphp

            @if($fornecedorUsuario)
                <a href="{{ route('fornecedores.show', $fornecedorUsuario) }}" class="btn btn-copav text-white border-none gap-2 shadow-sm font-medium shrink-0">
                    <span class="material-symbols-outlined text-xl">store</span>
                    Meu fornecedor
                </a>
            @else
                <a href="{{ route('fornecedores.create') }}" class="btn btn-copav text-white border-none gap-2 shadow-sm font-medium shrink-0">
                    <span class="material-symbols-outlined text-xl">add</span>
                    Cadastrar fornecedor
                </a>
            @endif
        @endauth
    </div>

    {{-- Alerta de Sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 rounded-r-md flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">check_circle</span>
                <span>{{ session('sucesso') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="btn btn-xs btn-circle btn-ghost text-emerald-800">✕</button>
        </div>
    @endif

    {{-- Grid de Fornecedores --}}
    @if($fornecedores->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($fornecedores as $fornecedor)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-between overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h2 class="text-lg font-bold text-gray-800 truncate">
                                {{ $fornecedor->nome }}
                            </h2>

                            @if($fornecedor->status)
                                <span class="badge bg-emerald-50 text-[#1B4D3E] border-emerald-200 text-xs font-medium shrink-0">
                                    {{ ucfirst($fornecedor->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400">badge</span>
                                <span class="truncate"><strong>Documento:</strong> {{ $fornecedor->documento }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400">call</span>
                                <span class="truncate"><strong>Telefone:</strong> {{ $fornecedor->telefone }}</span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400">location_on</span>
                                <span class="truncate">
                                    <strong>Local:</strong> {{ $fornecedor->cidade }}{{ $fornecedor->estado ? ' - ' . $fornecedor->estado : '' }}
                                </span>
                            </p>

                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-gray-400">person</span>
                                <span class="truncate">
                                    <strong>Usuário:</strong> {{ $fornecedor->usuario->nome ?? $fornecedor->usuario->name ?? 'Não informado' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <a href="{{ route('fornecedores.show', $fornecedor) }}" class="btn btn-ghost btn-xs text-[#1B4D3E] hover:bg-emerald-50 gap-1 font-medium">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            Ver detalhes
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300 p-8 my-6">
            <div class="w-16 h-16 bg-emerald-50 text-[#1B4D3E] rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">storefront</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Nenhum fornecedor cadastrado</h3>
            <p class="text-sm text-gray-500 mb-6">Ainda não existem fornecedores registrados no sistema.</p>
        </div>
    @endif

</div>

@endsection