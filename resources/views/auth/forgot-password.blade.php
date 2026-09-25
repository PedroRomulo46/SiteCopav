<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <p>Esqueceu sua senha? Sem problema. Nos informe seu endereço de email para enviarmos um link para reset de senha para você escolher uma nova.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button
            class="text-white font-semibold p-2 rounded-md"
            style="background-color: #1B4D3E">
                Enviar link para resetar senha
            </button>
        </div>
    </form>
</x-guest-layout>
