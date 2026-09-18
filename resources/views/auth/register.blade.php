<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome -->
        <div>
            <x-input-label for="nome" :value="__('Nome')" />

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
            <x-input-label for="email" :value="__('E-mail')" />

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

        <!-- Tipo de usuário -->
        <div class="mt-4">
            <x-input-label for="user_type" :value="__('Tipo de usuário')" />

            <select
                id="user_type"
                name="user_type"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
            >
                <option value="">Selecione uma opção</option>

                <option value="cliente" {{ old('user_type') == 'cliente' ? 'selected' : '' }}>
                    Cliente
                </option>

                <option value="fornecedor" {{ old('user_type') == 'fornecedor' ? 'selected' : '' }}>
                    Fornecedor
                </option>
            </select>

            <x-input-error
                :messages="$errors->get('user_type')"
                class="mt-2"
            />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

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
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />

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
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Já possui cadastro?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Cadastrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>