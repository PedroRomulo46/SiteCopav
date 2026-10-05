@extends('layouts.layout')
@section('title', 'Ofertas')

@section('conteudo')

{{-- Botão Voltar --}}
<div class="mb-4">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-[#236350] transition-colors">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Voltar para a página inicial
    </a>
</div>

<div class="max-w-7xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Meus Lotes
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Ofertas de produtos cadastradas no marketplace
            </p>
        </div>

        @auth
            <a
                href="{{ route('ofertas.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors shadow-sm">
                <span class="material-symbols-outlined text-xl">
                    add
                </span>
                Cadastrar oferta
            </a>
        @endauth
    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded-r-md">
            {{ session('sucesso') }}
        </div>
    @endif

    {{-- Lista de ofertas --}}
    <div
        id="lista-ofertas"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
    >
        @foreach($ofertas as $oferta)
            @include('ofertas.partials.card', [
                'oferta' => $oferta
            ])
        @endforeach
    </div>

    {{-- Nenhuma oferta --}}
    @if(!$ofertas->count())

        <div
            id="sem-ofertas"
            class="bg-white rounded-xl shadow-md border border-gray-100 p-10 text-center"
        >

            <span class="material-symbols-outlined text-6xl text-gray-300">
                local_offer
            </span>

            <h2 class="text-xl font-bold text-gray-700 mt-4">
                Nenhuma oferta cadastrada
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Ainda não existem ofertas cadastradas no marketplace.
            </p>

            @auth
                <a
                    href="{{ route('ofertas.create') }}"
                    class="inline-flex items-center gap-2 mt-5 bg-[#236350] hover:bg-[#1B4D3E] text-white px-5 py-2.5 rounded-md transition-colors"
                >
                    <span class="material-symbols-outlined">
                        add
                    </span>

                    Cadastrar primeira oferta
                </a>
            @endauth

        </div>

    @endif
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {

        if (!window.Echo) {
            console.error('Echo não foi carregado.');
            return;
        }

        window.Echo.channel('ofertas')
            .listen('.oferta.criada', function (event) {

                console.log('NOVA OFERTA RECEBIDA:', event);

                const lista = document.getElementById('lista-ofertas');

                if (!lista) {
                    console.error('Lista de ofertas não encontrada.');
                    return;
                }

                const ofertaId = event.oferta.id;

                if (lista.querySelector(`[data-oferta-id="${ofertaId}"]`)) {
                    return;
                }

                fetch(`/ofertas/${ofertaId}/card`)
                    .then(response => {

                        if (!response.ok) {
                            throw new Error(
                                'Erro ao carregar a nova oferta.'
                            );
                        }

                        return response.text();
                    })
                    .then(html => {

                        lista.insertAdjacentHTML(
                            'afterbegin',
                            html
                        );

                        const semOfertas =
                            document.getElementById('sem-ofertas');

                        if (semOfertas) {
                            semOfertas.remove();
                        }
                    })
                    .catch(error => {

                        console.error(
                            'Erro ao atualizar ofertas:',
                            error
                        );

                    });
            })
            .listen('.oferta.excluida', function (event) {

                console.log('OFERTA EXCLUÍDA:', event);

                const lista = document.getElementById('lista-ofertas');

                if (!lista) {
                    return;
                }

                const card = lista.querySelector(
                    `[data-oferta-id="${event.ofertaId}"]`
                );

                if (!card) {
                    console.log('Card da oferta não encontrado.');
                    return;
                }

                card.remove();

                console.log('Card removido da tela.');

            })
            .listen('.oferta.atualizada', function (event) {

                console.log('OFERTA ATUALIZADA:', event);

                const lista = document.getElementById('lista-ofertas');

                if (!lista) {
                    return;
                }

                const ofertaId = event.oferta.id;

                const cardAtual = lista.querySelector(
                    `[data-oferta-id="${ofertaId}"]`
                );

                if (!cardAtual) {
                    console.log('Card da oferta atualizada não encontrado.');
                    return;
                }

                fetch(`/ofertas/${ofertaId}/card`)
                    .then(response => {

                        if (!response.ok) {
                            throw new Error(
                                'Erro ao carregar a oferta atualizada.'
                            );
                        }

                        return response.text();
                    })
                    .then(html => {

                        cardAtual.outerHTML = html;

                        console.log('Card atualizado na tela.');

                    })
                    .catch(error => {

                        console.error(
                            'Erro ao atualizar card:',
                            error
                        );

                    });

            });

    });
</script>
@endpush