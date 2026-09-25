<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">

        @csrf
        <!-- Nome -->
        <div>
            <x-input-label for="nome" :value="__('Nome')"/>
            <x-text-input
                id="nome"
                class="block mt-1 w-full"
                type="text"
                name="nome"
                :value="old('nome')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error
                :messages="$errors->get('nome')"
                class="mt-2"
            />
        </div>
        <!-- E-mail -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail')"/>
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />
            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>
        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')"/>

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>
        <!-- Confirmar senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')"/>
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-end mt-4">
            <!-- Botão "já possui cadastro?" -->
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-sm focus:outline-none"
                href="{{ route('login') }}">
                {{ __('Já possui cadastro?') }}
            </a>
            <!-- Botão Cadastrar -->
            <button type="submit"
            class="ms-4 px-4 py-2 text-white font-semibold rounded-md shadow-sm"
            style="background-color: #236350">
                {{ __('Cadastrar') }}
            </button>
        </div>
    </form>
</x-guest-layout>