@extends('layouts.layout')

@section('title', 'Configurações')

@section('conteudo')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Configurações</h1>
        <p class="text-sm text-gray-500">Gerencie as informações da sua conta, senha e segurança.</p>
    </div>

    {{-- 1. INFORMAÇÕES DO PERFIL --}}
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

                {{-- FOTO DE PERFIL --}}
                <div class="form-control">
                    <label for="imagem" class="label">
                        <span class="label-text font-medium">Foto de perfil</span>
                    </label>

                    @if($user->imagem)
                        <div class="mt-1 mb-2" id="currentAvatarWrapper">
                            <img
                                id="avatarPreview"
                                src="{{ asset('storage/' . $user->imagem) }}"
                                alt="Foto de perfil"
                                class="w-32 h-32 rounded-full object-cover border-2 border-[#236350] block"
                            >
                        </div>
                    @else
                        <div class="mt-1 mb-2 hidden" id="currentAvatarWrapper">
                            <img
                                id="avatarPreview"
                                src=""
                                alt="Foto de perfil"
                                class="w-32 h-32 rounded-full object-cover border-2 border-[#236350] block"
                            >
                        </div>
                    @endif

                    <input
                        id="imagem"
                        name="imagem"
                        type="file"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="file-input file-input-bordered w-full max-w-md mt-1"
                        onchange="previewAvatar(event)"
                    >

                    <span class="label-text-alt text-gray-500 mt-1">
                        JPG, JPEG, PNG ou WEBP. Máximo de 2 MB.
                    </span>

                    @foreach ($errors->get('imagem') as $message)
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                {{-- NOME --}}
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

                {{-- EMAIL --}}
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

                {{-- BOTÃO SALVAR --}}
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

    {{-- 2. TROCAR SENHA --}}
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
                    {{-- SENHA ATUAL --}}
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

                    {{-- NOVA SENHA --}}
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

                    {{-- CONFIRMAR SENHA NOVA --}}
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

    {{-- 3. DELETAR CONTA (SEM X-MODAL) --}}
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
    function previewAvatar(event) {
        const file = event.target.files[0];
        const wrapper = document.getElementById('currentAvatarWrapper');
        const img = document.getElementById('avatarPreview');
        if (!file || !img || !wrapper) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            wrapper.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection