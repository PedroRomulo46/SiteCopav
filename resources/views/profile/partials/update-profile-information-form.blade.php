<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informações do perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Atualize as informações do seu perfil e o seu endereço de email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        {{-- FOTO DE PERFIL --}}
        <div>
            <x-input-label
                for="imagem"
                :value="__('Foto de perfil')"
            />

            @if($user->imagem)
                <div class="mt-2">
                    <img
                        src="{{ asset('storage/' . $user->imagem) }}"
                        alt="Foto de perfil"
                        style="
                            width: 128px;
                            height: 128px;
                            border-radius: 50%;
                            object-fit: cover;
                            display: block;
                            border: 1px solid black;">
                </div>
            @endif

            <input
                id="imagem"
                name="imagem"
                type="file"
                accept="image/jpeg,image/png,image/jpg,image/webp"
                class="block mt-2 w-full text-sm text-gray-900 border-none"
            >

            <p class="mt-1 text-sm text-gray-500">
                JPG, JPEG, PNG ou WEBP. Máximo de 2 MB.
            </p>

            <x-input-error
                class="mt-2"
                :messages="$errors->get('imagem')"
            />
        </div>

        {{-- NOME --}}
        <div>
            <x-input-label for="nome" :value="__('Nome')" />

            <x-text-input
                id="nome"
                name="nome"
                type="text"
                class="mt-1 block w-full"
                :value="old('nome', $user->nome)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('nome')"
            />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Seu endereço de email não foi verificado.

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clique aqui para re-enviar a verficação de email.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Um link de verficação foi enviado para o seu endereço de email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- BOTÃO SALVAR --}}
        <div class="flex items-center gap-4">
            <button type="submit"
            class="text-white px-4 py-2 rounded-md"
            style="background-color: #236350">
                Salvar
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">
                    Salvo.
                </p>
            @endif
        </div>
    </form>
</section>