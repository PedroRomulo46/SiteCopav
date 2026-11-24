@extends('layouts.layout')

@section('title', 'Configurações')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li>
            <a href="{{route('home')}}">
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="flex items-center space-x-2 hover:cursor-pointer">
            <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
            <span class="font-semibold text-gray-800">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configurações</h1>
        <p class="text-sm text-gray-500">Gerencie as informações da sua conta, senha e segurança.</p>
    </div>

    {{-- Informações do perfil --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body max-w-2xl">
            <h2 class="card-title text-lg text-gray-900">Informações do perfil</h2>
            <p class="text-sm text-gray-500">
                Atualize as informações do seu perfil e o seu endereço de email.
            </p>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form
                method="post"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
                class="mt-4 space-y-5"
            >
                @csrf
                @method('patch')

                {{-- Foto de perfil--}}
                <div class="form-control">
                    <span class="label-text font-medium mb-2 block">Foto de perfil</span>

                    <label
                        for="imagem"
                        title="Alterar foto de perfil"
                        class="relative w-32 h-32 rounded-full overflow-hidden border-2 border-[#236350] bg-gray-100 flex items-center justify-center cursor-pointer group select-none shadow-sm"
                    >
                        {{-- Imagem atual ou preview da nova imagem --}}
                        <img
                            id="avatarPreview"
                            src="{{ $user->imagem ? asset('storage/' . $user->imagem) : '' }}"
                            alt="Foto de perfil"
                            class="w-full h-full object-cover {{ $user->imagem ? 'block' : 'hidden' }}"
                        >

                        {{-- Ícone padrão quando não há imagem --}}
                        <span
                            id="avatarPlaceholder"
                            class="text-gray-400 {{ $user->imagem ? 'hidden' : 'flex' }} items-center justify-center w-full h-full"
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

                    <span class="label-text-alt text-gray-500 mt-2 block">
                        Clique na foto para selecionar. JPG, JPEG, PNG ou WEBP. Máximo de 2 MB.
                    </span>

                    @foreach ($errors->get('imagem') as $message)
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                {{-- Nome --}}
                <div class="form-control">
                    <label for="nome" class="label">
                        <span class="label-text font-medium">Nome</span>
                    </label>
                    <input
                        id="nome"
                        name="nome"
                        type="text"
                        value="{{ old('nome', $user->nome) }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="input input-bordered w-full max-w-md focus:border-[#236350] focus:outline-none"
                    >
                    @foreach ($errors->get('nome') as $message)
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                {{-- Email --}}
                <div class="form-control">
                    <label for="email" class="label">
                        <span class="label-text font-medium">Email</span>
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="username"
                        class="input input-bordered w-full max-w-md focus:border-[#236350] focus:outline-none"
                    >
                    @foreach ($errors->get('email') as $message)
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @endforeach

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning mt-3 text-sm">
                            <div>
                                <span>Seu endereço de email não foi verificado.</span>
                                <button type="submit" form="send-verification" class="link link-hover font-semibold underline ml-1">
                                    Clique aqui para reenviar a verificação de email.
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-success text-sm font-medium mt-2">
                                Um link de verificação foi enviado para o seu endereço de email.
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Botão de salvar --}}
                <div class="pt-2 flex items-center gap-4">
                    <button
                        type="submit"
                        class="btn text-white border-0 hover:opacity-95"
                        style="background-color: #236350;"
                    >
                        Salvar
                    </button>

                    @if (session('status') === 'profile-updated')
                        <span
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="badge badge-success badge-outline"
                        >
                            Salvo.
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Aparência / Modo Escuro --}}
    <div class="card bg-base-100 shadow-sm border border-base-200 dark:bg-gray-900 dark:border-gray-800">
        <div class="card-body max-w-2xl">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="card-title text-lg text-gray-900 dark:text-gray-100">Aparência</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Alterne entre o tema claro e o modo escuro em todo o site.
                    </p>
                </div>

                <button
                    type="button"
                    id="btnToggleDarkMode"
                    onclick="alternarModoEscuro()"
                    class="btn border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 gap-2 shrink-0 cursor-pointer"
                >
                    <span id="iconeModoEscuro" class="material-symbols-outlined text-xl">dark_mode</span>
                    <span id="textoModoEscuro">Ativar modo escuro</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Trocar senha --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <div class="max-w-xl">
                <h2 class="card-title text-lg text-gray-900">Trocar Senha</h2>
                <p class="text-sm text-gray-500 mt-1">
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
                    <div class="form-control">
                        <label for="update_password_current_password" class="label py-1">
                            <span class="label-text font-medium text-gray-700">Senha atual</span>
                        </label>
                        <input
                            id="update_password_current_password"
                            name="current_password"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input input-bordered w-full focus:border-[#236350] focus:outline-none"
                        >
                        @foreach ($errors->updatePassword->get('current_password') as $message)
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>

                    {{-- Nova senha --}}
                    <div class="form-control">
                        <label for="update_password_password" class="label py-1">
                            <span class="label-text font-medium text-gray-700">Nova senha</span>
                        </label>
                        <input
                            id="update_password_password"
                            name="password"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Mínimo de 8 caracteres"
                            class="input input-bordered w-full focus:border-[#236350] focus:outline-none"
                        >
                        @foreach ($errors->updatePassword->get('password') as $message)
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>

                    {{-- Confirmar nova senha --}}
                    <div class="form-control">
                        <label for="update_password_password_confirmation" class="label py-1">
                            <span class="label-text font-medium text-gray-700">Confirmar senha nova</span>
                        </label>
                        <input
                            id="update_password_password_confirmation"
                            name="password_confirmation"
                            :type="showPasswords ? 'text' : 'password'"
                            autocomplete="new-password"
                            placeholder="Repita a nova senha"
                            class="input input-bordered w-full focus:border-[#236350] focus:outline-none"
                        >
                        @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-sm text-gray-600">
                        <input
                            type="checkbox"
                            x-model="showPasswords"
                            class="checkbox checkbox-sm rounded border-gray-300 checked:border-[#236350]"
                            style="--chkbg: #236350; --chkfg: #ffffff;"
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
                                class="badge badge-success badge-outline"
                            >
                                Senha atualizada
                            </span>
                        @endif

                        <button
                            type="submit"
                            class="btn text-white border-0 hover:opacity-95 px-6"
                            style="background-color: #236350;"
                        >
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Deletar conta --}}
    <div class="card bg-base-100 shadow-sm border border-error/30">
        <div class="card-body max-w-2xl">
            <h2 class="card-title text-lg text-error">Deletar Conta</h2>
            <p class="text-sm text-gray-500">
                Assim que sua conta for deletada, todos os dados serão permanentemente apagados. Antes de deletar sua conta, faça o download de todas as informações que você deseja salvar.
            </p>

            <form
                method="post"
                action="{{ route('profile.destroy') }}"
                class="mt-4 p-4 rounded-box bg-error/5 border border-error/20 space-y-4 max-w-md"
                onsubmit="return confirm('Tem certeza que deseja deletar sua conta? Todos os seus dados serão perdidos permanentemente.');"
            >
                @csrf
                @method('delete')

                <p class="text-sm text-gray-700">
                    Todos os seus dados não baixados serão perdidos permanentemente, não sendo possível recuperá-los.
                </p>

                <div class="form-control">
                    <label for="delete_account_password" class="label">
                        <span class="label-text font-medium">Senha</span>
                    </label>
                    <input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        placeholder="Digite sua senha para confirmar"
                        required
                        class="input input-bordered w-full focus:border-error focus:outline-none"
                    >
                    @foreach ($errors->userDeletion->get('password') as $message)
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                <div class="pt-1">
                    <button type="submit" class="btn btn-error text-white">
                        Deletar Conta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function aplicarTemaEscuro(ativado) {
    const html = document.documentElement;
    const icone = document.getElementById('iconeModoEscuro');
    const texto = document.getElementById('textoModoEscuro');

    if (ativado) {
        html.classList.add('tema-escuro');
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('tema_escuro', '1');
        if (icone) icone.textContent = 'light_mode';
        if (texto) texto.textContent = 'Modo escuro ativado';
    } else {
        html.classList.remove('tema-escuro');
        html.setAttribute('data-theme', 'light');
        localStorage.setItem('tema_escuro', '0');
        if (icone) icone.textContent = 'dark_mode';
        if (texto) texto.textContent = 'Ativar modo escuro';
    }
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
        const file = event.target.files[0];
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