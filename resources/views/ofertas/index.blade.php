@extends('layouts.layout')

@section('title', 'Ofertas')

@section('conteudo')

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
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

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4">

    {{-- Cabeçalho --}}
    <div class="flex flex-row items-center justify-between gap-4 mb-6 border-b border-gray-200 dark:border-[#273449] pb-4">

        <div class="text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100">
                Meus Lotes
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Ofertas de produtos cadastradas no marketplace
            </p>
        </div>

        @auth
            <a
                href="{{ route('ofertas.create') }}"
                class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-white transition-colors shadow-sm shrink-0 text-sm font-semibold">
                <span class="material-symbols-outlined text-xl leading-none">
                    add
                </span>
                <span>Cadastrar oferta</span>
            </a>
        @endauth

    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-emerald-950/50 border-l-4 border-green-600 dark:border-emerald-400 text-green-700 dark:text-emerald-200 rounded-r-md text-left">
            {{ session('sucesso') }}
        </div>
    @endif

    {{-- Lista de ofertas --}}
    <div
        id="lista-ofertas"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 {{ $ofertas->count() ? '' : 'hidden' }}"
    >
        @foreach($ofertas as $oferta)
            @include('ofertas.partials.card', [
                'oferta' => $oferta
            ])
        @endforeach
    </div>

    {{-- Nenhuma oferta --}}
    <div
        id="sem-ofertas"
        class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-10 text-center my-6 {{ $ofertas->count() ? 'hidden' : '' }}"
    >
        <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 leading-none">
            local_offer
        </span>

        <h2 class="text-xl font-bold text-gray-700 dark:text-gray-100 mt-4">
            Nenhuma oferta cadastrada
        </h2>

        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
            Ainda não existem ofertas cadastradas no marketplace.
        </p>

        @auth
            <a
                href="{{ route('ofertas.create') }}"
                class="btn-copav inline-flex items-center justify-center gap-2 mt-5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors"
            >
                <span class="material-symbols-outlined text-lg leading-none">
                    add
                </span>

                <span>Cadastrar primeira oferta</span>
            </a>
        @endauth
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {
        if (!window.Echo) {
            console.error('Echo não foi carregado.');
            return;
        }

        const rotaCardOfertaTemplate = @json(route('ofertas.card', ['oferta' => '__ID__']));
        const lista = document.getElementById('lista-ofertas');
        const semOfertas = document.getElementById('sem-ofertas');

        function sincronizarEstadoVazio() {
            if (!lista || !semOfertas) return;

            const temCards = lista.querySelectorAll('[data-oferta-id]').length > 0;
            lista.classList.toggle('hidden', !temCards);
            semOfertas.classList.toggle('hidden', temCards);
        }

        window.Echo.channel('ofertas')
            .listen('.oferta.criada', function (event) {
                const ofertaId = event?.oferta?.id;

                if (!lista || !ofertaId) {
                    return;
                }

                if (lista.querySelector(`[data-oferta-id="${ofertaId}"]`)) {
                    return;
                }

                fetch(rotaCardOfertaTemplate.replace('__ID__', ofertaId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar a nova oferta.');
                        }

                        return response.text();
                    })
                    .then(html => {
                        lista.insertAdjacentHTML('afterbegin', html);
                        sincronizarEstadoVazio();
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar ofertas:', error);
                    });
            })
            .listen('.oferta.excluida', function (event) {
                if (!lista || !event?.ofertaId) {
                    return;
                }

                const card = lista.querySelector(
                    `[data-oferta-id="${event.ofertaId}"]`
                );

                if (!card) {
                    return;
                }

                card.remove();
                sincronizarEstadoVazio();
            })
            .listen('.oferta.atualizada', function (event) {
                const ofertaId = event?.oferta?.id;

                if (!lista || !ofertaId) {
                    return;
                }

                const cardAtual = lista.querySelector(
                    `[data-oferta-id="${ofertaId}"]`
                );

                if (!cardAtual) {
                    return;
                }

                fetch(rotaCardOfertaTemplate.replace('__ID__', ofertaId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar a oferta atualizada.');
                        }

                        return response.text();
                    })
                    .then(html => {
                        cardAtual.outerHTML = html;
                        sincronizarEstadoVazio();
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar card:', error);
                    });
            });
    });
</script>
@endpush