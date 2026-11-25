@extends('layouts.layout')

@section('title', 'Meu Perfil')

@section('conteudo')

@php
    $imagemPerfil = $user?->imagem
        ? (str_contains($user->imagem, 'assets/')
            ? asset($user->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $user->imagem), '/')))
        : null;
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E]">
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-5xl mx-auto px-4 py-8 space-y-6 text-left">

    {{-- Cabeçalho da página --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Meu Perfil</h1>
            <p class="text-sm text-gray-500">Visualize os dados principais da sua conta.</p>
        </div>

        <a
            href="{{ route('profile.edit') }}"
            class="btn-copav inline-flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-95"
        >
            <span class="material-symbols-outlined text-lg">settings</span>
            Editar configurações
        </a>
    </div>

    {{-- Cartão principal do perfil --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                {{-- Foto de perfil --}}
                <div class="shrink-0">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-[#236350] bg-gray-100 flex items-center justify-center shadow-sm">
                        @if($imagemPerfil)
                            <img
                                id="perfilAvatarImg"
                                src="{{ $imagemPerfil }}"
                                alt="Foto de perfil de {{ $user?->nome ?? 'Usuário' }}"
                                class="w-full h-full object-cover"
                                onerror="this.classList.add('hidden'); document.getElementById('perfilAvatarPlaceholder')?.classList.remove('hidden');"
                            >
                            <span id="perfilAvatarPlaceholder" class="hidden text-gray-400 items-center justify-center w-full h-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </span>
                        @else
                            <span class="text-gray-400 flex items-center justify-center w-full h-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Informações do usuário --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $user?->nome ?? 'Usuário' }}</h2>
                        <p class="text-sm text-gray-500">{{ $user?->email ?? '—' }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-base-200 max-w-2xl">
                        <div>
                            <span class="text-xs uppercase tracking-wider text-gray-400 font-medium block">
                                Nome completo
                            </span>
                            <span class="text-sm font-medium text-gray-800 mt-0.5 block">
                                {{ $user?->nome ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs uppercase tracking-wider text-gray-400 font-medium block">
                                Endereço de email
                            </span>
                            <span class="text-sm font-medium text-gray-800 mt-0.5 block">
                                {{ $user?->email ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs uppercase tracking-wider text-gray-400 font-medium block">
                                Status do email
                            </span>
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <span class="badge badge-warning badge-outline mt-1">Não verificado</span>
                            @else
                                <span class="badge badge-success badge-outline mt-1">Verificado</span>
                            @endif
                        </div>

                        @if($user?->created_at)
                            <div>
                                <span class="text-xs uppercase tracking-wider text-gray-400 font-medium block">
                                    Membro desde
                                </span>
                                <span class="text-sm font-medium text-gray-800 mt-0.5 block">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection