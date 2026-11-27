@extends('layouts.layout')

@section('chat_layout', true)

@section('conteudo')
<div class="flex h-full min-h-0 w-full flex-col overflow-hidden">
    <div
        x-data="chatIndex()"
        x-init="inicializar()"
        class="flex min-h-0 flex-1 overflow-hidden rounded-none border-y border-gray-200 bg-white shadow-sm"
    >
        {{-- PAINEL ESQUERDO --}}
        <aside
            class="flex min-h-0 w-full flex-col overflow-hidden border-r border-gray-200 bg-white md:w-[340px] md:shrink-0 lg:w-[390px]"
            :class="conversaSelecionada ? 'hidden md:flex' : 'flex'"
        >
            
            <div class="shrink-0 border-b border-gray-100 px-4 py-4">
                {{-- TÍTULO E VOLTAR --}}
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        onclick="voltarParaOrigemChat()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Voltar"
                    >
                        <span class="material-symbols-outlined text-[23px]">arrow_back</span>
                    </button>

                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-gray-900">
                            Suas conversas
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $chats->count() }} {{ $chats->count() === 1 ? 'conversa' : 'conversas' }}
                        </p>
                    </div>
                </div>

                {{-- PESQUISA --}}
                <div class="relative mt-4 w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-gray-400">
                        search
                    </span>

                    <input
                        type="search"
                        x-model="busca"
                        placeholder="Pesquisar contato, assunto ou produto..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-10 pr-4 text-sm text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#236350] focus:bg-white focus:ring-1 focus:ring-[#236350]"
                    >
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">

            @if ($chats->isEmpty())
                <div class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-4xl text-gray-300">forum</span>

                    <h3 class="mt-3 font-semibold text-gray-800">
                        Nenhuma conversa por enquanto
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Quando uma proposta for aceita, sua conversa ficará disponível aqui.
                    </p>

                    <a
                        href="{{ route('negociacoes.index') }}"
                        class="mt-5 inline-flex rounded-lg bg-[#236350] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#1B4D3E]"
                    >
                        Ver negociações
                    </a>
                </div>
            @else

            {{-- LISTA: UMA ENTRADA POR CONTATO --}}
            @forelse ($chats as $chat)
                @php
                    $ehCliente = (int) $chat->cliente_id === (int) auth()->id();

                    $nomeParticipante = $ehCliente
                        ? (
                            $chat->fornecedor?->usuario?->nome
                            ?? $chat->fornecedor?->nome
                            ?? 'Fornecedor'
                        )
                        : ($chat->cliente?->nome ?? 'Cliente');

                    $inicial = mb_strtoupper(mb_substr($nomeParticipante, 0, 1));
                @endphp

                <button
                    type="button"
                    x-show="!busca.trim()"
                    @click="abrirContato({{ $chat->id }})"
                    :class="Number(conversaSelecionada?.chat_id) === {{ $chat->id }}
                        ? 'border-l-4 border-[#236350] bg-[#E5EFEA]/70'
                        : 'border-l-4 border-transparent hover:bg-gray-50'"
                    class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-4 text-left transition"
                >
                    {{-- AVATAR --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] text-lg font-semibold text-[#1B4D3E]">
                        {{ $inicial }}
                    </div>

                    {{-- INFORMAÇÕES DO CONTATO --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="truncate text-sm font-semibold text-gray-900">
                                {{ $nomeParticipante }}
                            </h3>

                            <span
                                x-show="conversas.some(item => item.chat_id === {{ $chat->id }} && item.ultima_mensagem_data)"
                                x-text="formatarHoraLista(
                                    conversas
                                        .filter(item => item.chat_id === {{ $chat->id }})
                                        .sort((a, b) => new Date(b.ultima_mensagem_data || 0) - new Date(a.ultima_mensagem_data || 0))[0]?.ultima_mensagem_data
                                )"
                                class="shrink-0 text-[10px] text-gray-400"
                            ></span>
                        </div>

                        <p
                            class="mt-1 truncate text-xs text-gray-500"
                            x-text="previsualizacaoContato({{ $chat->id }})"
                        >
                            Nenhuma mensagem ainda
                        </p>

                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">
                                {{ $chat->assuntos->count() }} assuntos vinculados
                            </span>

                            <span
                                x-show="totalNaoLidasContato({{ $chat->id }}) > 0"
                                x-cloak
                                x-text="totalNaoLidasContato({{ $chat->id }})"
                                class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full border border-[#236350]/20 bg-[#E5EFEA] px-1 text-[10px] font-bold text-[#1B4D3E]"
                            ></span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-4xl text-gray-300">forum</span>

                    <h3 class="mt-3 font-semibold text-gray-800">
                        Nenhuma conversa por enquanto
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Quando uma proposta for aceita, sua conversa ficará disponível aqui.
                    </p>

                    <a
                        href="{{ route('negociacoes.index') }}"
                        class="mt-5 inline-flex rounded-lg bg-[#236350] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#1B4D3E]"
                    >
                        Ver negociações
                    </a>
                </div>
            @endforelse

            {{-- RESULTADOS DA PESQUISA --}}
            <div
                x-show="busca.trim().length > 0"
                x-cloak
                class="divide-y divide-gray-100"
            >
                <template x-for="contexto in resultadosBusca()" :key="contexto.contexto_chave">
                    <button
                        type="button"
                        @click="selecionarContexto(contexto.contexto_chave)"
                        class="flex w-full items-center gap-3 px-4 py-4 text-left transition hover:bg-[#E5EFEA]/50"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] text-[#236350]">
                            <span
                                class="material-symbols-outlined"
                                x-text="contexto.assunto_chat_id ? 'handshake' : 'chat'"
                            ></span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p
                                    class="truncate text-sm font-semibold text-gray-900"
                                    x-text="contexto.participante"
                                ></p>

                                <span
                                    x-show="Number(naoLidas[contexto.contexto_chave] || 0) > 0"
                                    class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full border border-[#236350]/20 bg-[#E5EFEA] px-1 text-[10px] font-bold text-[#1B4D3E]"
                                    x-text="naoLidas[contexto.contexto_chave]"
                                ></span>
                            </div>

                            <p
                                class="mt-0.5 truncate text-xs font-medium text-[#236350]"
                                x-text="contexto.titulo"
                            ></p>

                            <p
                                class="mt-1 truncate text-xs text-gray-500"
                                x-text="resumoContexto(contexto)"
                            ></p>
                        </div>

                        <span class="material-symbols-outlined shrink-0 text-gray-400">
                            chevron_right
                        </span>
                    </button>
                </template>

                <div
                    x-show="resultadosBusca().length === 0"
                    class="px-5 py-10 text-center"
                >
                    <span class="material-symbols-outlined text-3xl text-gray-300">search_off</span>
                    <p class="mt-2 text-sm font-medium text-gray-600">
                        Nenhum resultado encontrado
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        Tente pesquisar pelo nome, assunto ou produto.
                    </p>
                </div>
            </div>

        @endif

            {{-- DADOS DOS CONTEXTOS: USADOS PELO JAVASCRIPT --}}
            <div class="hidden" aria-hidden="true">
                @foreach ($chats as $chat)
                    @php
                        $ehCliente = (int) $chat->cliente_id === (int) auth()->id();

                        $nomeParticipante = $ehCliente
                            ? (
                                $chat->fornecedor?->usuario?->nome
                                ?? $chat->fornecedor?->nome
                                ?? 'Fornecedor'
                            )
                            : ($chat->cliente?->nome ?? 'Cliente');
                    @endphp

                    {{-- MENSAGENS GERAIS --}}
                    <button
                        type="button"
                        data-contexto-chat-id="{{ $chat->id }}"
                        data-assunto-chat-id=""
                        data-contexto-chave="{{ $chat->id }}:geral"
                        data-contexto-titulo="Mensagens gerais"
                        data-participante="{{ $nomeParticipante }}"
                        data-oferta-url=""
                        data-imagem-url=""
                        data-negociacoes="[]"
                    ></button>

                    {{-- ASSUNTOS --}}
                    @foreach ($chat->assuntos as $assunto)
                        @php
                            $primeiraOferta = $assunto->negociacoes
                                ->map(fn ($negociacao) => $negociacao->oferta)
                                ->filter()
                                ->first();

                            $produto = $primeiraOferta?->produto;

                            $imagemProduto = $produto?->imagem
                                ? asset('storage/' . ltrim($produto->imagem, '/'))
                                : '';

                            $negociacoesVinculadas = $assunto->negociacoes
                                ->map(function ($negociacao) {
                                    $produto = $negociacao->oferta?->produto;

                                    return [
                                        'id' => $negociacao->id,
                                        'titulo' => 'Negociação #' . $negociacao->id,
                                        'produto' => $produto?->nome ?? 'Negociação',
                                        'url' => route('negociacoes.show', $negociacao),
                                    ];
                                })
                                ->values();
                        @endphp

                        <button
                            type="button"
                            data-contexto-chat-id="{{ $chat->id }}"
                            data-assunto-chat-id="{{ $assunto->id }}"
                            data-contexto-chave="{{ $chat->id }}:{{ $assunto->id }}"
                            data-contexto-titulo="{{ $assunto->titulo }}"
                            data-participante="{{ $nomeParticipante }}"
                            data-oferta-url="{{ $primeiraOferta ? route('ofertas.show', $primeiraOferta) : '' }}"
                            data-imagem-url="{{ $imagemProduto }}"
                            data-negociacoes="{{ $negociacoesVinculadas->toJson() }}"
                        ></button>
                    @endforeach
                @endforeach
            </div>
        </div>
        </aside>

        {{-- PAINEL DIREITO --}}
        <section
            class="min-h-0 min-w-0 flex-1 flex-col overflow-hidden bg-[#F5F5F0]"
            :class="conversaSelecionada ? 'flex' : 'hidden md:flex'"
        >
            {{-- Estado inicial --}}
            <div
                x-show="!conversaSelecionada"
                class="flex flex-1 flex-col items-center justify-center px-6 text-center"
            >
                <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#E5EFEA] text-[#236350]">
                    <span class="material-symbols-outlined text-5xl">forum</span>
                </div>

                <h2 class="mt-5 text-xl font-semibold text-gray-800">Suas mensagens</h2>

                <p class="mt-2 max-w-sm text-sm leading-relaxed text-gray-500">
                    Selecione uma conversa ao lado para visualizar as mensagens e continuar sua negociação.
                </p>

                <div class="mt-6 flex items-center gap-2 text-xs text-gray-400">
                    <span class="material-symbols-outlined text-base">lock</span>
                    Suas conversas são privadas.
                </div>
            </div>

            {{-- Conversa selecionada --}}
            <div
                x-show="conversaSelecionada"
                class="flex min-h-0 flex-1 flex-col"
                style="display: none;"
            >
                <header class="flex min-h-[72px] shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <button
                        type="button"
                        @click="voltarLista()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350] md:hidden"
                        title="Voltar às conversas"
                        aria-label="Voltar às conversas"
                    >
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                        <span x-text="conversaSelecionada?.participante?.charAt(0).toUpperCase() || '?'"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2
                            class="truncate font-semibold text-gray-900"
                            x-text="conversaSelecionada?.participante"
                        ></h2>

                        <p
                            class="truncate text-sm font-medium text-[#1B4D3E]"
                            x-text="conversaSelecionada?.titulo"
                        ></p>
                    </div>

                    {{-- A loja acompanha a negociação selecionada --}}
                    <a
                        :href="urlNegociacaoSelecionada()"
                        :target="urlNegociacaoSelecionada() !== '#' ? '_blank' : null"
                        :aria-disabled="urlNegociacaoSelecionada() === '#'"
                        :tabindex="urlNegociacaoSelecionada() !== '#' ? 0 : -1"
                        :class="urlNegociacaoSelecionada() !== '#'
                            ? 'text-gray-600 hover:bg-gray-100 hover:text-[#236350]'
                            : 'pointer-events-none cursor-not-allowed text-gray-300 opacity-50'"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition"
                        :title="urlNegociacaoSelecionada() !== '#'
                            ? 'Acessar a negociação selecionada'
                            : 'Nenhuma negociação vinculada a esta conversa'"
                        aria-label="Acessar negociação selecionada"
                    >
                        <span class="material-symbols-outlined">storefront</span>
                    </a>

                    <a
                        :href="conversaSelecionada
                            ? '/chat/' + conversaSelecionada.chat_id
                                + (conversaSelecionada.assunto_chat_id
                                    ? '?assunto_chat_id=' + conversaSelecionada.assunto_chat_id
                                    : '')
                            : '#'"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Abrir conversa em página completa"
                        aria-label="Abrir conversa em página completa"
                    >
                        <span class="material-symbols-outlined">open_in_new</span>
                    </a>
                </header>

                {{-- CONVERSAS E NEGOCIAÇÕES ENTRE OS PARTICIPANTES --}}
                <div class="shrink-0 border-b border-gray-200 bg-white px-4 py-2 sm:px-6">
                    <button
                        type="button"
                        @click="cardNegociacaoAberto = !cardNegociacaoAberto"
                        class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-sm font-medium text-[#236350] transition hover:bg-[#E5EFEA]/60"
                    >
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">
                                handshake
                            </span>

                            Conversas e negociações

                            <span
                                class="rounded-full bg-[#E5EFEA] px-2 py-0.5 text-[10px] font-semibold text-[#236350]"
                                x-text="contextosDoChat().length"
                            ></span>
                        </span>

                        <span
                            class="material-symbols-outlined text-[20px] transition-transform"
                            :class="cardNegociacaoAberto ? 'rotate-180' : ''"
                        >
                            expand_more
                        </span>
                    </button>

                    <div
                        x-show="cardNegociacaoAberto"
                        x-transition
                        x-cloak
                        class="mt-1 space-y-2 pb-2"
                    >

                        <template
                            x-for="contexto in contextosDoChat()"
                            :key="contexto.contexto_chave"
                        >
                            <button
                                type="button"
                                @click="selecionarContexto(contexto.contexto_chave)"
                                class="flex w-full items-center gap-3 rounded-xl border px-3 py-3 text-left transition"
                                :class="contextoSelecionado === contexto.contexto_chave
                                    ? 'border-[#236350] bg-[#E5EFEA]/70'
                                    : 'border-gray-100 bg-white hover:border-gray-300 hover:bg-gray-50'"
                            >
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                                    :class="contexto.assunto_chat_id
                                        ? 'bg-[#E5EFEA] text-[#236350]'
                                        : 'bg-gray-100 text-gray-500'"
                                >
                                    <span
                                        class="material-symbols-outlined text-[19px]"
                                        x-text="contexto.assunto_chat_id ? 'handshake' : 'chat'"
                                    ></span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate text-sm font-semibold text-gray-800"
                                        x-text="contexto.titulo"
                                    ></p>

                                    <p
                                        class="mt-0.5 truncate text-xs text-gray-500"
                                        x-text="resumoContexto(contexto)"
                                    ></p>
                                </div>

                                <span
                                    x-show="Number(naoLidas[contexto.contexto_chave] || 0) > 0"
                                    x-cloak
                                    class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full border border-[#236350]/20 bg-[#E5EFEA] px-1 text-[10px] font-bold text-[#1B4D3E]"
                                    x-text="naoLidas[contexto.contexto_chave]"
                                ></span>

                                <span
                                    x-show="contextoSelecionado === contexto.contexto_chave"
                                    class="material-symbols-outlined shrink-0 text-[19px] text-[#236350]"
                                >
                                    check_circle
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- LIBERAÇÃO DAS MENSAGENS GERAIS --}}
                <div
                    x-show="mensagensGeraisBloqueadas()"
                    x-cloak
                    class="flex flex-1 flex-col items-center justify-center bg-[#F5F5F0] px-6 py-10 text-center"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#E5EFEA] text-[#236350]">
                        <span class="material-symbols-outlined text-3xl">lock</span>
                    </div>

                    <h3 class="mt-4 text-base font-semibold text-gray-800">
                        Conversa geral
                    </h3>

                    <p
                        x-show="conversaSelecionada?.mensagens_gerais_status === 'inativo'"
                        class="mt-2 max-w-sm text-sm leading-relaxed text-gray-500"
                    >
                        Para trocar mensagens gerais, é necessário solicitar a liberação ao outro participante.
                    </p>

                    <p
                        x-show="conversaSelecionada?.mensagens_gerais_status === 'solicitado'
                            && conversaSelecionada?.mensagens_gerais_sou_solicitante"
                        class="mt-2 max-w-sm text-sm leading-relaxed text-gray-500"
                    >
                        Sua solicitação foi enviada. Aguarde o outro participante aprovar o acesso às mensagens gerais.
                    </p>

                    <p
                        x-show="conversaSelecionada?.mensagens_gerais_status === 'solicitado'
                            && conversaSelecionada?.mensagens_gerais_pode_aprovar"
                        class="mt-2 max-w-sm text-sm leading-relaxed text-gray-500"
                    >
                        O outro participante solicitou a liberação das mensagens gerais. Você pode aprovar a solicitação abaixo.
                    </p>

                    <button
                        x-show="conversaSelecionada?.mensagens_gerais_status === 'inativo'"
                        type="button"
                        @click="solicitarMensagensGerais()"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#236350] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1B4D3E]"
                    >
                        <span class="material-symbols-outlined text-lg">send</span>
                        Solicitar chat geral
                    </button>

                    <button
                        x-show="conversaSelecionada?.mensagens_gerais_status === 'solicitado'
                            && conversaSelecionada?.mensagens_gerais_pode_aprovar"
                        type="button"
                        @click="aprovarMensagensGerais()"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#236350] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1B4D3E]"
                    >
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        Aprovar solicitação
                    </button>

                    <p
                        x-show="erroEnvio"
                        x-text="erroEnvio"
                        class="mt-4 text-sm text-red-600"
                    ></p>
                </div>

                {{-- Histórico --}}
                <div
                    x-ref="listaMensagens"
                    x-show="!mensagensGeraisBloqueadas()"
                    class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain px-4 py-5 sm:px-6"
                    style="visibility: hidden;"
                >
                    <div
                        x-show="carregandoMensagens"
                        class="py-8 text-center text-sm text-gray-500"
                    >
                        Carregando mensagens...
                    </div>

                    <div
                        x-show="erroMensagens"
                        class="mx-auto max-w-md rounded-lg bg-red-50 p-3 text-center text-sm text-red-600"
                        x-text="erroMensagens"
                    ></div>
          
                    <template x-for="mensagem in mensagens" :key="mensagem.id">
                        <div class="space-y-2">
                            {{-- DIVISÓRIA DE NOVAS MENSAGENS --}}
                            <div
                                x-show="
                                    marcadorNovasMensagens.id !== null &&
                                    Number(mensagem.id) === Number(marcadorNovasMensagens.id)
                                "
                                x-cloak
                                class="flex items-center gap-3 py-3"
                            >
                                <div class="h-px flex-1 bg-[#236350]"></div>

                                <span class="flex h-5 min-w-5 shrink-0 items-center justify-center gap-1 rounded-full border border-[#236350]/20 bg-[#E5EFEA] px-2 text-[10px] font-bold text-[#1B4D3E]">
                                    Novas mensagens:
                                    <span x-text="marcadorNovasMensagens.quantidade"></span>
                                </span>

                                <div class="h-px flex-1 bg-[#236350]"></div>
                            </div>

                            {{-- MENSAGEM --}}
                            <div
                                class="flex"
                                :class="Number(mensagem.usuario_id) === usuarioId
                                    ? 'justify-end'
                                    : 'justify-start'"
                            >
                                <div
                                    class="max-w-[88%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[75%]"
                                    :class="Number(mensagem.usuario_id) === usuarioId
                                        ? 'rounded-br-sm bg-[#D9F2DF] text-gray-800'
                                        : 'rounded-bl-sm bg-white text-gray-800'"
                                >
                                    <p
                                        class="whitespace-pre-wrap wrap-break-words text-sm leading-relaxed"
                                        x-text="mensagem.conteudo"
                                    ></p>

                                    <p
                                        class="mt-2 text-right text-[11px] text-gray-500"
                                        x-text="formatarDataHora(mensagem.created_at)"
                                    ></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div
                        x-show="!carregandoMensagens && mensagens.length === 0 && !erroMensagens"
                        class="py-12 text-center"
                    >
                        <p class="text-sm text-gray-500">Nenhuma mensagem ainda.</p>
                        <p class="mt-1 text-xs text-gray-400">
                            Envie uma mensagem para iniciar a conversa.
                        </p>
                    </div>
                </div>

                {{-- Erro de envio --}}
                <div
                    x-show="erroEnvio"
                    class="border-t border-red-100 bg-red-50 px-4 py-2 text-sm text-red-600"
                    x-text="erroEnvio"
                ></div>

                {{-- Formulário --}}
                <form
                    x-show="!mensagensGeraisBloqueadas()"
                    @submit.prevent="enviarMensagem()"
                    class="flex shrink-0 items-end gap-2 border-t border-gray-200 bg-white px-3 py-3 sm:gap-3 sm:px-5"
                >
                    <textarea
                        x-model="mensagemNova"
                        @keydown.enter="if ($event.ctrlKey || $event.metaKey) { $event.preventDefault(); enviarMensagem(); }"
                        rows="1"
                        maxlength="5000"
                        placeholder="Digite sua mensagem..."
                        class="h-11 min-h-11 flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 caret-[#1B4D3E] outline-none transition focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                    ></textarea>

                    <div class="flex shrink-0 flex-col items-center gap-1">
                        <span class="hidden text-[11px] text-gray-400 sm:block">
                            Ctrl + Enter
                        </span>

                        <button
                            type="submit"
                            :disabled="!mensagemNova.trim() || enviandoMensagem"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
                            aria-label="Enviar mensagem"
                            :title="enviandoMensagem ? 'Enviando mensagem...' : 'Enviar mensagem'"
                        >
                            <span
                                x-show="!enviandoMensagem"
                                class="material-symbols-outlined text-xl"
                            >send</span>

                            <span
                                x-show="enviandoMensagem"
                                x-cloak
                                class="material-symbols-outlined animate-spin text-xl"
                            >progress_activity</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>

// Guarda a página de origem ao entrar no índice do chat.
(function () {
    const chave = 'sitecopav_chat_index_origem';
    const origem = document.referrer;

    if (!origem) return;

    try {
        const url = new URL(origem);

        // Só aceita páginas do próprio Site Copav.
        if (url.origin !== window.location.origin) return;

        // Não substitui a origem ao voltar do show para o index.
        if (url.pathname === '/chat' || url.pathname.startsWith('/chat/')) {
            return;
        }

        sessionStorage.setItem(chave, url.href);
    } catch (erro) {
        console.warn('Não foi possível registrar a página de origem.', erro);
    }
})();

// Retorna à página de origem, com fallback para a página inicial.
window.voltarParaOrigemChat = function () {
    const chave = 'sitecopav_chat_index_origem';

    try {
        const origem = sessionStorage.getItem(chave);

        if (origem) {
            const url = new URL(origem);

            if (url.origin === window.location.origin) {
                window.location.assign(url.href);
                return;
            }
        }
    } catch (erro) {
        console.warn('Não foi possível recuperar a página de origem.', erro);
    }

    window.location.assign('/');
};

window.chatIndex = function () {
    return {
        usuarioId: {{ (int) auth()->id() }},
        csrfToken: @json(csrf_token()),

        busca: '',
        conversas: [],
        conversaSelecionada: null,
        assuntoSelecionadoId: '',
        contextoSelecionado: '',
        cardNegociacaoAberto: false,
        negociacaoSelecionadaId: '',
        mensagens: [],
        marcadorNovasMensagens: {
            id: null,
            quantidade: 0
        },
        mensagemNova: '',

        carregandoMensagens: false,
        enviandoMensagem: false,
        erroMensagens: '',
        erroEnvio: '',

        naoLidas: {},
        canaisInscritos: [],
        requisicaoMensagens: 0,

        
        async inicializar() {
            this.conversas = Array.from(
                this.$el.querySelectorAll('[data-contexto-chat-id]')
            ).map(botao => {
                const chatId = Number(botao.dataset.contextoChatId);
                const assuntoId = botao.dataset.assuntoChatId || null;

                return {
                    chat_id: chatId,
                    assunto_chat_id: assuntoId ? Number(assuntoId) : null,
                    contexto_chave: botao.dataset.contextoChave,
                    titulo: botao.dataset.contextoTitulo,
                    participante: botao.dataset.participante || 'Participante',
                    oferta_url: botao.dataset.ofertaUrl || '',
                    imagem: botao.dataset.imagemUrl || '',
                    negociacoes: JSON.parse(botao.dataset.negociacoes || '[]'),

                    mensagens_gerais_status: 'inativo',
                    mensagens_gerais_sou_solicitante: false,
                    mensagens_gerais_pode_aprovar: false,

                    ultima_mensagem: 'Nenhuma mensagem ainda',
                    ultima_mensagem_usuario: null,
                    ultima_mensagem_data: null,
                    elemento: botao
                };
            });

            await this.carregarContadores();

            const parametros = new URLSearchParams(window.location.search);

            const chatId = parametros.get('chat_id');
            const assuntoId = parametros.get('assunto_chat_id');

            if (!chatId) return;

            const contexto = this.conversas.find(item =>
                item.chat_id === Number(chatId) &&
                item.assunto_chat_id === (assuntoId ? Number(assuntoId) : null)
            );

            if (!contexto) {
                console.warn('Não foi encontrado o contexto solicitado no chat.');
                return;
            }

            await this.abrirConversa(contexto.chat_id, contexto.elemento);
        },

        itensLista() {
            return [...this.conversas].sort((a, b) => {
                const dataA = a.ultima_mensagem_data
                    ? new Date(a.ultima_mensagem_data).getTime()
                    : 0;

                const dataB = b.ultima_mensagem_data
                    ? new Date(b.ultima_mensagem_data).getTime()
                    : 0;

                return dataB - dataA;
            });
        },

        normalizarTexto(texto) {
            return String(texto || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        },

        resultadosBusca() {
            const termo = this.normalizarTexto(this.busca);

            if (!termo) return [];

            return this.conversas
                .map(contexto => {
                    const negociacoes = contexto.negociacoes || [];

                    const termosPesquisa = [
                        contexto.participante,
                        contexto.titulo,
                        this.resumoContexto(contexto),
                        ...negociacoes.map(item => item.titulo),
                        ...negociacoes.map(item => item.produto)
                    ];

                    const corresponde = termosPesquisa.some(texto =>
                        this.normalizarTexto(texto).includes(termo)
                    );

                    return corresponde ? contexto : null;
                })
                .filter(Boolean)
                .sort((a, b) => {
                    const dataA = a.ultima_mensagem_data
                        ? new Date(a.ultima_mensagem_data).getTime()
                        : 0;

                    const dataB = b.ultima_mensagem_data
                        ? new Date(b.ultima_mensagem_data).getTime()
                        : 0;

                    return dataB - dataA;
                });
        },

        
        contextosDoChat() {
            if (!this.conversaSelecionada) return [];

            return this.conversas
                .filter(
                    item =>
                        Number(item.chat_id) ===
                        Number(this.conversaSelecionada.chat_id)
                )
                .sort((a, b) => {
                    const dataA = a.ultima_mensagem_data
                        ? new Date(a.ultima_mensagem_data).getTime()
                        : 0;

                    const dataB = b.ultima_mensagem_data
                        ? new Date(b.ultima_mensagem_data).getTime()
                        : 0;

                    return dataB - dataA;
                });
        },

        negociacoesDisponiveis() {
            const negociacoes = this.contextosDoChat()
                .filter(contexto => contexto.assunto_chat_id !== null)
                .flatMap(contexto =>
                    (contexto.negociacoes || []).map(negociacao => ({
                        ...negociacao,
                        contexto_chave: contexto.contexto_chave
                    }))
                );

            // Evita mostrar a mesma negociação mais de uma vez.
            return negociacoes.filter(
                (item, index, lista) =>
                    lista.findIndex(
                        outra => Number(outra.id) === Number(item.id)
                    ) === index
            );
        },

        resumoContexto(contexto) {
            if (!contexto) return '';

            if (!contexto.assunto_chat_id) {
                return 'Mensagens gerais';
            }

            const produtos = (contexto.negociacoes || [])
                .map(item => item.produto)
                .filter(Boolean);

            if (produtos.length === 0) {
                return 'Conversa de negociação';
            }

            return [...new Set(produtos)].join(', ');
        },

        abrirContato(chatId) {
            const contextoGeral = this.conversas.find(
                item =>
                    Number(item.chat_id) === Number(chatId) &&
                    item.assunto_chat_id === null
            );

            if (!contextoGeral) {
                console.error('Mensagens gerais não encontradas para o contato:', chatId);
                return;
            }

            this.abrirConversa(chatId, contextoGeral.elemento);
        },

        selecionarContexto(chave) {
            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (!contexto) return;

            this.abrirConversa(contexto.chat_id, contexto.elemento);
        },

        async selecionarNegociacao(id) {
            if (!id || !this.conversaSelecionada) return;

            const chatId = this.conversaSelecionada.chat_id;

            const contexto = this.conversas.find(item =>
                Number(item.chat_id) === Number(chatId) &&
                item.assunto_chat_id !== null &&
                (item.negociacoes || []).some(
                    negociacao => String(negociacao.id) === String(id)
                )
            );

            if (!contexto) return;

            await this.abrirConversa(
                contexto.chat_id,
                contexto.elemento
            );

            // Preserva a negociação escolhida, mesmo quando
            // existem várias negociações vinculadas ao mesmo assunto.
            this.negociacaoSelecionadaId = String(id);
        },

        urlNegociacaoSelecionada() {
            if (!this.conversaSelecionada) return '#';

            const negociacoes = this.conversaSelecionada.negociacoes || [];

            const selecionada = negociacoes.find(
                item => String(item.id) === String(this.negociacaoSelecionadaId)
            );

            return selecionada?.url || '#';
        },

        totalNaoLidasContato(chatId) {
            return this.conversas
                .filter(item => Number(item.chat_id) === Number(chatId))
                .reduce(
                    (total, item) =>
                        total + Number(this.naoLidas[item.contexto_chave] || 0),
                    0
                );
        },

        previsualizacaoContato(chatId) {
            const contextos = this.conversas.filter(
                item => Number(item.chat_id) === Number(chatId)
            );

            if (!contextos.length) return 'Nenhuma mensagem ainda';

            const maisRecente = [...contextos].sort((a, b) => {
                const dataA = a.ultima_mensagem_data
                    ? new Date(a.ultima_mensagem_data).getTime()
                    : 0;

                const dataB = b.ultima_mensagem_data
                    ? new Date(b.ultima_mensagem_data).getTime()
                    : 0;

                return dataB - dataA;
            })[0];

            return `${maisRecente.titulo}: ${this.textoPrevia(maisRecente)}`;
        },


        async carregarContadores() {
            try {
                const resposta = await fetch(@json(route('chat.conversas')), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!resposta.ok) {
                    throw new Error('Não foi possível carregar as conversas.');
                }

                const dados = await resposta.json();

                dados.forEach(item => {
                    // Mensagens gerais
                    const geral = this.conversas.find(
                        contexto =>
                            contexto.chat_id === Number(item.chat_id) &&
                            contexto.assunto_chat_id === null
                    );

                    if (geral && item.mensagens_gerais) {
                        geral.mensagens_gerais_status =
                            item.mensagens_gerais.status || 'inativo';

                        geral.mensagens_gerais_sou_solicitante =
                            Boolean(item.mensagens_gerais.sou_solicitante);

                        geral.mensagens_gerais_pode_aprovar =
                            Boolean(item.mensagens_gerais.pode_aprovar);

                        geral.ultima_mensagem =
                            item.mensagens_gerais.ultima_mensagem;

                        geral.ultima_mensagem_usuario =
                            item.mensagens_gerais.ultima_mensagem_usuario;

                        geral.ultima_mensagem_data =
                            item.mensagens_gerais.ultima_mensagem_data;

                        this.naoLidas[geral.contexto_chave] =
                            Number(item.mensagens_gerais.mensagens_nao_lidas || 0);
                    }

                    // Assuntos
                    (item.assuntos || []).forEach(assunto => {
                        const contexto = this.conversas.find(
                            entrada =>
                                entrada.chat_id === Number(item.chat_id) &&
                                entrada.assunto_chat_id === Number(assunto.id)
                        );

                        if (!contexto) return;

                        contexto.ultima_mensagem = assunto.ultima_mensagem;
                        contexto.ultima_mensagem_usuario =
                            assunto.ultima_mensagem_usuario;
                        contexto.ultima_mensagem_data =
                            assunto.ultima_mensagem_data;

                        this.naoLidas[contexto.contexto_chave] =
                            Number(assunto.mensagens_nao_lidas || 0);
                    });
                });

            } catch (erro) {
                console.error('Erro ao carregar contadores:', erro);
            }

            this.conversas.forEach(contexto => {
                this.inscreverCanal(contexto.chat_id);
            });
        },

        textoPrevia(contexto) {
            if (!contexto) return '';

            const prefixo =
                Number(contexto.ultima_mensagem_usuario) === this.usuarioId
                    ? 'Você: '
                    : '';

            return prefixo + (
                contexto.ultima_mensagem || 'Nenhuma mensagem ainda'
            );
        },

        inscreverCanal(chatId) {
            if (!window.Echo || !chatId) return;

            chatId = Number(chatId);

            if (this.canaisInscritos.includes(chatId)) return;

            this.canaisInscritos.push(chatId);

            window.Echo.private(`chat.${chatId}`)
                .listen('.mensagem.enviada', mensagem => {
                    this.receberMensagem(mensagem);
                })
                .listen('.mensagens.gerais.atualizadas', dados => {
                    this.receberAtualizacaoMensagensGerais(dados);
                });
        },

        receberMensagem(mensagem) {
            const chave = mensagem.assunto_chat_id
                ? `${mensagem.chat_id}:${mensagem.assunto_chat_id}`
                : `${mensagem.chat_id}:geral`;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (!contexto) return;

            contexto.ultima_mensagem = mensagem.conteudo;
            contexto.ultima_mensagem_usuario = mensagem.usuario_id;
            contexto.ultima_mensagem_data = mensagem.created_at;

            const mesmoContexto =
                this.contextoSelecionado === chave;

            if (
                mesmoContexto &&
                !this.carregandoMensagens
            ) {
                this.adicionarMensagem(mensagem);
            } else if (Number(mensagem.usuario_id) !== this.usuarioId) {
                this.naoLidas[chave] =
                    Number(this.naoLidas[chave] || 0) + 1;
            }
        },

        mensagensGeraisAtivas() {
            return this.conversaSelecionada?.assunto_chat_id === null
                && this.conversaSelecionada?.mensagens_gerais_status === 'ativo';
        },

        mensagensGeraisBloqueadas() {
            return this.conversaSelecionada?.assunto_chat_id === null
                && this.conversaSelecionada?.mensagens_gerais_status !== 'ativo';
        },

        async solicitarMensagensGerais() {
            if (!this.conversaSelecionada || this.enviandoMensagem) return;

            const chatId = this.conversaSelecionada.chat_id;

            try {
                this.erroEnvio = '';

                const resposta = await fetch(
                    `/chat/${chatId}/mensagens-gerais/solicitar`,
                    {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível solicitar a liberação.'
                    );
                }

                this.conversaSelecionada.mensagens_gerais_status = 'solicitado';
                this.conversaSelecionada.mensagens_gerais_sou_solicitante = true;
                this.conversaSelecionada.mensagens_gerais_pode_aprovar = false;

            } catch (erro) {
                this.erroEnvio = erro.message;
            }
        },

        async receberAtualizacaoMensagensGerais(dados) {
            const contexto = this.conversas.find(item =>
                Number(item.chat_id) === Number(dados.chat_id) &&
                item.assunto_chat_id === null
            );

            if (!contexto) return;

            contexto.mensagens_gerais_status = dados.status || 'inativo';

            contexto.mensagens_gerais_sou_solicitante =
                Number(dados.solicitado_por) === this.usuarioId;

            contexto.mensagens_gerais_pode_aprovar =
                dados.status === 'solicitado' &&
                Number(dados.solicitado_por) !== this.usuarioId;

            // Se a liberação aconteceu enquanto o usuário estava
            // visualizando as mensagens gerais, carrega o histórico.
            if (
                dados.status === 'ativo' &&
                this.contextoSelecionado === contexto.contexto_chave
            ) {
                await this.carregarMensagens();
            }
        },

        async aprovarMensagensGerais() {
            if (!this.conversaSelecionada) return;

            const chatId = this.conversaSelecionada.chat_id;

            try {
                this.erroEnvio = '';

                const resposta = await fetch(
                    `/chat/${chatId}/mensagens-gerais/aprovar`,
                    {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível aprovar a conversa.'
                    );
                }

                this.conversaSelecionada.mensagens_gerais_status = 'ativo';
                this.conversaSelecionada.mensagens_gerais_sou_solicitante = false;
                this.conversaSelecionada.mensagens_gerais_pode_aprovar = false;

                await this.carregarMensagens();

            } catch (erro) {
                this.erroEnvio = erro.message;
            }
        },

        async abrirConversa(chatId, botao) {
            chatId = Number(chatId);

            if (!botao) {
                console.error('Botão do contexto não encontrado:', chatId);
                return;
            }

            const chave = botao.dataset.contextoChave;
            const assuntoId = botao.dataset.assuntoChatId || null;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (!contexto) {
                console.error('Contexto não encontrado:', chave);
                return;
            }
            if (this.$refs.listaMensagens) {
                this.$refs.listaMensagens.style.visibility = 'hidden';
            }
            this.conversaSelecionada = contexto;
            this.contextoSelecionado = chave;
            this.assuntoSelecionadoId = assuntoId || '';
            this.negociacaoSelecionadaId =
                contexto.negociacoes?.[0]?.id || '';

            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';

            this.inscreverCanal(chatId);

            await this.carregarMensagens();
        },

        async carregarMensagens() {
            if (!this.conversaSelecionada) return;

            const numeroRequisicao = ++this.requisicaoMensagens;

            this.carregandoMensagens = true;
            this.erroMensagens = '';
            this.mensagens = [];

            const chatId = this.conversaSelecionada.chat_id;
            const assuntoId = this.assuntoSelecionadoId;
            const chave = this.contextoSelecionado;

            try {
                let url = `/chat/${chatId}/mensagens`;

                if (assuntoId) {
                    url += `?assunto_chat_id=${encodeURIComponent(assuntoId)}`;
                }

                const resposta = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível carregar as mensagens.'
                    );
                }

                // Impede uma resposta antiga de substituir outro contexto.
                if (
                    numeroRequisicao === this.requisicaoMensagens &&
                    this.contextoSelecionado === chave
                ) {
                    // Atualiza o estado da conversa geral, quando for esse contexto.
                    if (this.conversaSelecionada?.assunto_chat_id === null) {
                        this.conversaSelecionada.mensagens_gerais_status =
                            dados.status || this.conversaSelecionada.mensagens_gerais_status;

                        this.conversaSelecionada.mensagens_gerais_sou_solicitante =
                            Boolean(dados.sou_solicitante);

                        this.conversaSelecionada.mensagens_gerais_pode_aprovar =
                            Boolean(dados.pode_aprovar);
                    }

                    this.mensagens = dados.mensagens || [];

                    this.marcadorNovasMensagens = {
                        id: dados.primeira_mensagem_nao_lida_id || null,
                        quantidade: Number(dados.mensagens_nao_lidas || 0)
                    };

                    // O controller já marcou somente este contexto como lido.
                    this.naoLidas[chave] = 0;
                }

            } catch (erro) {
                if (numeroRequisicao === this.requisicaoMensagens) {
                    this.erroMensagens = erro.message;
                }
            } finally {
                if (numeroRequisicao === this.requisicaoMensagens) {
                    this.carregandoMensagens = false;

                    this.$nextTick(() => {
                        this.rolarHistorico(true);
                    });
                }
            }
        },

        adicionarMensagem(mensagem) {
            const existe = this.mensagens.some(
                item => Number(item.id) === Number(mensagem.id)
            );

            if (!existe) {
                this.mensagens.push(mensagem);
            }

            const chave = mensagem.assunto_chat_id
                ? `${mensagem.chat_id}:${mensagem.assunto_chat_id}`
                : `${mensagem.chat_id}:geral`;

            const contexto = this.conversas.find(
                item => item.contexto_chave === chave
            );

            if (contexto) {
                contexto.ultima_mensagem = mensagem.conteudo;
                contexto.ultima_mensagem_usuario = mensagem.usuario_id;
                contexto.ultima_mensagem_data = mensagem.created_at;
            }
        },

        async enviarMensagem() {
            const conteudo = this.mensagemNova.trim();

            if (
                !conteudo ||
                !this.conversaSelecionada ||
                this.enviandoMensagem
            ) return;

            this.enviandoMensagem = true;
            this.erroEnvio = '';

            try {
                const resposta = await fetch(
                    `/chat/${this.conversaSelecionada.chat_id}/mensagens`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            conteudo,
                            assunto_chat_id: this.assuntoSelecionadoId || null
                        })
                    }
                );

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível enviar a mensagem.'
                    );
                }

                this.adicionarMensagem(dados.mensagem);
                this.mensagemNova = '';

                this.$nextTick(() => this.rolarHistorico());

            } catch (erro) {
                this.erroEnvio = erro.message;
            } finally {
                this.enviandoMensagem = false;
            }
        },

        voltarLista() {
            this.requisicaoMensagens++;

            this.conversaSelecionada = null;
            this.contextoSelecionado = '';
            this.assuntoSelecionadoId = '';
            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';
            this.carregandoMensagens = false;
        },

        rolarHistorico(revelar = false) {
            const lista = this.$refs.listaMensagens;

            if (!lista) return;

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    lista.scrollTop = lista.scrollHeight;

                    if (revelar) {
                        lista.style.visibility = 'visible';
                    }
                });
            });
        },

        formatarDataHora(data) {
            if (!data) return '';

            const dataMensagem = new Date(data);

            if (isNaN(dataMensagem.getTime())) return '';

            return dataMensagem.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).replace(',', ' às');
        },

        formatarHoraLista(data) {
            if (!data) return '';

            const dataMensagem = new Date(data);

            if (isNaN(dataMensagem.getTime())) return '';

            return dataMensagem.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
};


</script>
@endpush
