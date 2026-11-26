@extends('layouts.layout')

@section('title', 'Configurações')

@section('conteudo')

@php
    $imagemPerfil = $user?->imagem
        ? (str_contains($user->imagem, 'assets/')
            ? asset($user->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $user->imagem), '/')))
        : null;
@endphp

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

<div class="max-w-5xl mx-auto px-4 py-8 space-y-6 text-left">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Configurações</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Gerencie as informações da sua conta, senha e segurança.
        </p>
    </div>

    {{-- Informações do perfil --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl shadow-sm border border-gray-200 dark:border-[#273449]">
        <div class="p-6 max-w-2xl">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Informações do perfil</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Atualize as informações do seu perfil e o seu endereço de email.
            </p>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form
                method="post"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
                class="mt-5 space-y-5"
            >
                @csrf
                @method('patch')

                {{-- Foto de perfil --}}
                <div>
                    <span class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Foto de perfil</span>

                    <label
                        for="imagem"
                        title="Alterar foto de perfil"
                        class="relative w-32 h-32 rounded-full overflow-hidden border-2 border-[#236350] dark:border-emerald-500 bg-gray-100 dark:bg-slate-800 flex items-center justify-center cursor-pointer group select-none shadow-sm"
                    >
                        {{-- Imagem atual ou preview da nova imagem --}}
                        <img
                            id="avatarPreview"
                            src="{{ $imagemPerfil ?? '' }}"
                            alt="Foto de perfil"
                            class="w-full h-full object-cover {{ $imagemPerfil ? 'block' : 'hidden' }}"
                            onerror="this.classList.add('hidden'); this.classList.remove('block'); document.getElementById('avatarPlaceholder')?.classList.remove('hidden'); document.getElementById('avatarPlaceholder')?.classList.add('flex');"
                        >

                        {{-- Ícone padrão quando não há imagem --}}
                        <span
                            id="avatarPlaceholder"
                            class="text-gray-400 dark:text-gray-500 {{ $imagemPerfil ? 'hidden' : 'flex' }} items-center justify-center w-full h-full"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>

                        {{-- Sobreposição com lápis ao passar o mouse --}}
                        <div
                            class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            <span class="text-xs font-medium mt-1">Alterar</span>
                        </div>
                    </label>

                    <input
                        id="imagem"
                        name="imagem"
                        type="file"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="hidden"
                        onchange="previewAvatar(event)"
                    >

                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Clique na foto para selecionar. JPG, JPEG, PNG ou WEBP. Máximo de 2 MB.
                    </span>

                    @foreach ($errors->get('imagem') as $message)
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                {{-- Nome --}}
                <div>
                    <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Nome
                    </label>
                    <input
                        id="nome"
                        name="nome"
                        type="text"
                        value="{{ old('nome', $user?->nome) }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full max-w-md px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('nome') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    >
                    @foreach ($errors->get('nome') as $message)
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user?->email) }}"
                        required
                        autocomplete="username"
                        class="w-full max-w-md px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->has('email') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                    >
                    @foreach ($errors->get('email') as $message)
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endforeach

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 rounded-lg border border-amber-200 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-950/40 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
                            <div>
                                <span>Seu endereço de email não foi verificado.</span>
                                <button type="submit" form="send-verification" class="font-semibold underline ml-1 cursor-pointer">
                                    Clique aqui para reenviar a verificação de email.
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-emerald-700 dark:text-emerald-400 text-sm font-medium mt-2">
                                Um link de verificação foi enviado para o seu endereço de email.
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Botão de salvar --}}
                <div class="pt-2 flex items-center gap-4">
                    <button
                        type="submit"
                        class="btn-copav inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-95 cursor-pointer"
                    >
                        Salvar
                    </button>

                    @if (session('status') === 'profile-updated')
                        <span
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="inline-flex items-center rounded-full border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"
                        >
                            Salvo.
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Aparência / Modo Escuro --}}
    <div id="cardAparencia" class="bg-white dark:bg-[#111827] rounded-2xl shadow-sm border border-gray-200 dark:border-[#273449] transition-colors duration-200">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <h2 id="tituloAparencia" class="text-lg font-bold text-gray-900 dark:text-gray-100">Aparência</h2>
                    <p id="descricaoAparencia" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Alterne entre o tema claro e o modo escuro em todo o site.
                    </p>
                </div>

                <button
                    type="button"
                    id="btnToggleDarkMode"
                    onclick="alternarModoEscuro()"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-slate-800 shrink-0 cursor-pointer transition-colors"
                >
                    <span id="iconeModoEscuro" class="material-symbols-outlined text-xl leading-none">dark_mode</span>
                    <span id="textoModoEscuro">Ativar modo escuro</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Trocar senha --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl shadow-sm border border-gray-200 dark:border-[#273449]">
        <div class="p-6">
            <div class="max-w-xl">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Trocar Senha</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Garanta que sua conta utiliza uma senha longa e segura.
                </p>
            </div>

            <form
                method="post"
                action="{{ route('password.update') }}"
                class="mt-6 space-y-5"
                x-data="{ showPasswords: false }"
            >
                @csrf
                @method('put')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Senha atual --}}
                    <div>
                        <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Senha atual
                        </label>
                        <input
                            id="update_password_current_password"
                            name="current_password"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->updatePassword->has('current_password') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        >
                        @foreach ($errors->updatePassword->get('current_password') as $message)
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>

                    {{-- Nova senha --}}
                    <div>
                        <label for="update_password_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Nova senha
                        </label>
                        <input
                            id="update_password_password"
                            name="password"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Mínimo de 8 caracteres"
                            class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->updatePassword->has('password') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        >
                        @foreach ($errors->updatePassword->get('password') as $message)
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>

                    {{-- Confirmar nova senha --}}
                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Confirmar senha nova
                        </label>
                        <input
                            id="update_password_password_confirmation"
                            name="password_confirmation"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Repita a nova senha"
                            class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-[#236350] dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20"
                        >
                        @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-sm text-gray-600 dark:text-gray-300">
                        <input
                            type="checkbox"
                            x-model="showPasswords"
                            class="h-4 w-4 rounded border-gray-300 dark:border-slate-600 text-[#236350] focus:ring-[#236350] cursor-pointer"
                        >
                        <span>Mostrar senhas</span>
                    </label>

                    <div class="flex items-center gap-3">
                        @if (session('status') === 'password-updated')
                            <span
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 3000)"
                                class="inline-flex items-center rounded-full border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"
                            >
                                Senha atualizada
                            </span>
                        @endif

                        <button
                            type="submit"
                            class="btn-copav inline-flex items-center justify-center rounded-lg px-6 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-95 cursor-pointer"
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Deletar conta --}}
    <div class="bg-white dark:bg-[#111827] rounded-2xl shadow-sm border border-red-200 dark:border-red-900/60">
        <div class="p-6 max-w-2xl">
            <h2 class="text-lg font-bold text-red-600 dark:text-red-400">Deletar Conta</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Assim que sua conta for deletada, todos os dados serão permanentemente apagados. Antes de deletar sua conta, faça o download de todas as informações que você deseja salvar.
            </p>

            <form
                method="post"
                action="{{ route('profile.destroy') }}"
                class="mt-4 p-4 rounded-xl bg-red-50/70 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 space-y-4 max-w-md"
                onsubmit="return confirm('Tem certeza que deseja deletar sua conta? Todos os seus dados serão perdidos permanentemente.');"
            >
                @csrf
                @method('delete')

                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Todos os seus dados não baixados serão perdidos permanentemente, não sendo possível recuperá-los.
                </p>

                <div>
                    <label for="delete_account_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Senha
                    </label>
                    <input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        placeholder="Digite sua senha para confirmar"
                        required
                        class="w-full px-3.5 py-2.5 rounded-lg text-sm bg-white dark:bg-[#161f30] text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 border {{ $errors->userDeletion->has('password') ? 'border-red-500 dark:border-red-400' : 'border-gray-300 dark:border-[#273449]' }} outline-none transition focus:border-red-500 dark:focus:border-red-400 focus:ring-2 focus:ring-red-500/20"
                    >
                    @foreach ($errors->userDeletion->get('password') as $message)
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                <div class="pt-1">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-red-600 hover:bg-red-700 px-5 py-2.5 text-sm font-semibold text-white transition-colors cursor-pointer"
                    >
                        Deletar Conta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function sincronizarBotaoTema(ativado) {
        const icone = document.getElementById('iconeModoEscuro');
        const texto = document.getElementById('textoModoEscuro');

        if (icone) icone.textContent = ativado ? 'light_mode' : 'dark_mode';
        if (texto) texto.textContent = ativado ? 'Modo escuro ativado' : 'Ativar modo escuro';
    }

    function aplicarTemaEscuro(ativado) {
        const html = document.documentElement;

        if (ativado) {
            html.classList.add('tema-escuro');
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('tema_escuro', '1');
        } else {
            html.classList.remove('tema-escuro');
            html.setAttribute('data-theme', 'light');
            localStorage.setItem('tema_escuro', '0');
        }

        sincronizarBotaoTema(ativado);
    }

    function alternarModoEscuro() {
        const estaEscuro = document.documentElement.classList.contains('tema-escuro');
        aplicarTemaEscuro(!estaEscuro);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const salvo = localStorage.getItem('tema_escuro') === '1';
        aplicarTemaEscuro(salvo);
    });

    function previewAvatar(event) {
        const file = event.target.files?.[0];
        const img = document.getElementById('avatarPreview');
        const placeholder = document.getElementById('avatarPlaceholder');
        if (!file || !img) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            img.classList.remove('hidden');
            img.classList.add('block');

            if (placeholder) {
                placeholder.classList.remove('flex');
                placeholder.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection