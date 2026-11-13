
@extends('layouts.layout')

@section('chat_layout', true)

@section('conteudo')
<div class="flex h-full min-h-0 w-full flex-col overflow-hidden">

    {{-- Interface de mensagens --}}
    <div
        x-data="chatIndex()"
        x-init="inicializar()"
        class="flex min-h-0 flex-1 overflow-hidden rounded-none border-y border-gray-200 bg-white shadow-sm"
    >

        {{-- PAINEL ESQUERDO: CONVERSAS --}}
        <aside
            class="min-h-0 w-full flex-col overflow-hidden border-r border-gray-200 bg-white md:w-[340px] md:shrink-0 lg:w-[390px]"
            :class="conversaSelecionada ? 'hidden md:flex' : 'flex'"
        >

            {{-- Cabeçalho da lista --}}
            <div class="shrink-0 border-b border-gray-100 px-5 py-4">

                <div class="flex items-center gap-3">

                    {{-- Voltar --}}
                    <button
                        type="button"
                        onclick="history.back()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Voltar"
                        aria-label="Voltar à página anterior"
                    >
                        <span class="material-symbols-outlined text-[23px]">
                            arrow_back
                        </span>
                    </button>

                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Suas conversas
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $chats->count() }}
                            {{ $chats->count() === 1 ? 'conversa' : 'conversas' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- Lista --}}
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">

                @forelse ($chats as $chat)
                    @php
                        $negociacao = $chat->negociacao;
                        $ehCliente = (int) $negociacao->cliente_id === (int) auth()->id();

                        $outroParticipante = $ehCliente
                            ? $negociacao->oferta->fornecedor
                            : $negociacao->cliente;

                        $ultimaMensagem = $chat->mensagens->first();

                        $nomeParticipante = $ehCliente
                            ? ($outroParticipante->nome ?? 'Fornecedor')
                            : ($outroParticipante->nome ?? 'Cliente');

                        $nomeProduto = $negociacao->oferta->produto->nome
                            ?? 'Produto da negociação';
                    @endphp

                    <button
                        type="button"
                        @click="abrirConversa($event.currentTarget)"
                        data-chat-id="{{ $chat->id }}"
                        data-negociacao-id="{{ $negociacao->id }}"
                        data-participante="{{ $nomeParticipante }}"
                        data-produto="{{ $nomeProduto }}"
                        data-ultima-mensagem="{{ $ultimaMensagem?->conteudo ?? 'Nenhuma mensagem ainda' }}"
                        data-ultima-mensagem-usuario="{{ $ultimaMensagem?->usuario_id ?? '' }}"
                        data-ultima-mensagem-data="{{ $ultimaMensagem?->created_at?->toISOString() ?? '' }}"
                        class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-4 text-left transition hover:bg-gray-50"
                        :class="conversaSelecionada && Number(conversaSelecionada.chat_id) === {{ (int) $chat->id }} ? 'bg-[#EAF2ED]' : ''"
                    >

                        {{-- Avatar --}}
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                            {{ mb_strtoupper(mb_substr($nomeParticipante, 0, 1)) }}
                        </div>

                        {{-- Informações --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="truncate text-sm font-semibold text-gray-900">
                                    {{ $nomeParticipante }}
                                </h3>

                                <span class="shrink-0 text-[11px] text-gray-400"
                                    x-text="formatarHoraLista(
                                        $el.closest('button').dataset.ultimaMensagemData
                                    )"
                                ></span>
                            </div>

                            <p class="mt-1 truncate text-xs text-[#236350]">
                                {{ $nomeProduto }}
                            </p>

                            <div class="mt-1 flex items-center justify-between gap-2">
                                <p
                                    class="truncate text-xs text-gray-500"
                                    x-text="textoPrevia({{ (int) $chat->id }})"
                                >
                                    {{ $ultimaMensagem
                                        ? (($ultimaMensagem->usuario_id == auth()->id() ? 'Você: ' : '') . $ultimaMensagem->conteudo)
                                        : 'Nenhuma mensagem ainda' }}
                                </p>

                                <span
                                    x-show="naoLidas[{{ (int) $chat->id }}] > 0"
                                    class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-[#236350] px-1 text-[10px] font-bold text-white"
                                    x-text="naoLidas[{{ (int) $chat->id }}]"
                                ></span>
                            </div>
                        </div>
                    </button>

                @empty
                    <div class="px-6 py-16 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300">
                            forum
                        </span>

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

            </div>
        </aside>

        {{-- PAINEL DIREITO: CONVERSA --}}
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
                    <span class="material-symbols-outlined text-5xl">
                        forum
                    </span>
                </div>

                <h2 class="mt-5 text-xl font-semibold text-gray-800">
                    Suas mensagens
                </h2>

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

                {{-- Cabeçalho --}}
                <header class="flex min-h-[72px] items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">

                    <button
                        type="button"
                        @click="voltarLista()"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 md:hidden"
                        aria-label="Voltar às conversas"
                    >
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E5EFEA] font-semibold text-[#1B4D3E]">
                        <span x-text="conversaSelecionada?.participante?.charAt(0).toUpperCase()"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2
                            class="truncate font-semibold text-gray-900"
                            x-text="conversaSelecionada?.participante"
                        ></h2>

                        <p
                            class="truncate text-xs text-gray-500"
                            x-text="conversaSelecionada?.produto"
                        ></p>
                    </div>

                    <a
                        :href="conversaSelecionada ? '/chat/' + conversaSelecionada.negociacao_id : '#'"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#236350]"
                        title="Abrir conversa em página completa"
                    >
                        <span class="material-symbols-outlined">open_in_new</span>
                    </a>
                </header>

                {{-- Histórico --}}
                <div
                    x-ref="listaMensagens"
                    class="min-h-0 flex-1 space-y-3 overflow-y-auto overscroll-contain px-3 py-5 sm:px-6"
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
                        <div
                            class="flex"
                            :class="Number(mensagem.usuario_id) === usuarioId ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-[88%] rounded-2xl px-4 py-2.5 shadow-sm sm:max-w-[75%]"
                                :class="Number(mensagem.usuario_id) === usuarioId
                                    ? 'rounded-br-sm bg-[#D9F2DF] text-gray-800'
                                    : 'rounded-bl-sm bg-white text-gray-800'"
                            >
                                <p
                                    class="whitespace-pre-wrap break-words text-sm leading-relaxed"
                                    x-text="mensagem.conteudo"
                                ></p>

                                <p
                                    class="mt-1 text-right text-[10px] text-gray-500"
                                    x-text="formatarDataHora(mensagem.created_at)"
                                ></p>
                            </div>
                        </div>
                    </template>

                    <div
                        x-show="!carregandoMensagens && mensagens.length === 0 && !erroMensagens"
                        class="py-12 text-center"
                    >
                        <p class="text-sm text-gray-500">
                            Nenhuma mensagem ainda.
                        </p>
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
                    @submit.prevent="enviarMensagem()"
                    class="flex shrink-0 items-end gap-2 border-t border-gray-200 bg-white px-3 py-3 sm:px-5"
                >
                    <textarea
                        x-model="mensagemNova"
                        @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); enviarMensagem(); }"
                        rows="1"
                        maxlength="5000"
                        placeholder="Digite sua mensagem..."
                        class="max-h-32 min-h-[44px] flex-1 resize-none rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#236350] focus:ring-1 focus:ring-[#236350]"
                    ></textarea>

                    <button
                        type="submit"
                        :disabled="!mensagemNova.trim() || enviandoMensagem"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#236350] text-white transition hover:bg-[#1B4D3E] disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Enviar mensagem"
                    >
                        <span
                            x-show="!enviandoMensagem"
                            class="material-symbols-outlined"
                        >
                            send
                        </span>

                        <span
                            x-show="enviandoMensagem"
                            class="material-symbols-outlined animate-spin"
                        >
                            progress_activity
                        </span>
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.chatIndex = function () {
    return {
        usuarioId: {{ (int) auth()->id() }},
        csrfToken: @json(csrf_token()),

        conversas: [],
        conversaSelecionada: null,
        mensagens: [],
        mensagemNova: '',

        carregandoMensagens: false,
        enviandoMensagem: false,
        erroMensagens: '',
        erroEnvio: '',

        naoLidas: {},
        canaisInscritos: [],

        inicializar() {
            this.conversas = Array.from(
                this.$el.querySelectorAll('[data-chat-id]')
            ).map(botao => ({
                chat_id: Number(botao.dataset.chatId),
                negociacao_id: Number(botao.dataset.negociacaoId),
                participante: botao.dataset.participante,
                produto: botao.dataset.produto,
                ultima_mensagem: botao.dataset.ultimaMensagem,
                ultima_mensagem_usuario: botao.dataset.ultimaMensagemUsuario
            }));

            this.carregarContadores();
        },

        async carregarContadores() {
            try {
                const resposta = await fetch(@json(route('chat.conversas')), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!resposta.ok) return;

                const dados = await resposta.json();

                dados.forEach(item => {
                    this.naoLidas[item.chat_id] =
                        Number(item.mensagens_nao_lidas || 0);
                });

            } catch (erro) {
                console.error('Erro ao carregar contadores:', erro);
            }

            this.conversas.forEach(conversa => {
                this.inscreverCanal(conversa.chat_id);
            });
        },

        textoPrevia(chatId) {
            const conversa = this.conversas.find(
                item => Number(item.chat_id) === Number(chatId)
            );

            if (!conversa) return '';

            const prefixo =
                Number(conversa.ultima_mensagem_usuario) === this.usuarioId
                    ? 'Você: '
                    : '';

            return prefixo + (conversa.ultima_mensagem || 'Nenhuma mensagem ainda');
        },

        inscreverCanal(chatId) {
            if (!window.Echo || !chatId) return;

            chatId = Number(chatId);

            if (this.canaisInscritos.includes(chatId)) return;

            this.canaisInscritos.push(chatId);

            window.Echo.private(`chat.${chatId}`)
                .listen('.mensagem.enviada', mensagem => {
                    this.receberMensagem(mensagem);
                });
        },

        receberMensagem(mensagem) {
            const conversa = this.conversas.find(
                item => Number(item.chat_id) === Number(mensagem.chat_id)
            );

            if (!conversa) return;

            conversa.ultima_mensagem = mensagem.conteudo;
            conversa.ultima_mensagem_usuario = mensagem.usuario_id;

            if (
                this.conversaSelecionada &&
                Number(this.conversaSelecionada.chat_id) === Number(mensagem.chat_id)
            ) {
                this.adicionarMensagem(mensagem);
                this.rolarHistorico();
            } else if (Number(mensagem.usuario_id) !== this.usuarioId) {
                this.naoLidas[mensagem.chat_id] =
                    Number(this.naoLidas[mensagem.chat_id] || 0) + 1;
            }
        },

        async abrirConversa(botao) {
            const chatId = Number(botao.dataset.chatId);

            const conversa = this.conversas.find(
                item => Number(item.chat_id) === chatId
            );

            if (!conversa) return;

            this.conversaSelecionada = conversa;
            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';
            this.carregandoMensagens = true;

            this.naoLidas[chatId] = 0;

            try {
                const resposta = await fetch(
                    `/chat/${conversa.negociacao_id}/mensagens`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                const dados = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(
                        dados.message || 'Não foi possível carregar as mensagens.'
                    );
                }

                this.mensagens = dados.mensagens || [];

                this.$nextTick(() => this.rolarHistorico());

            } catch (erro) {
                this.erroMensagens = erro.message;
            } finally {
                this.carregandoMensagens = false;
            }
        },

        adicionarMensagem(mensagem) {
            const existe = this.mensagens.some(
                item => Number(item.id) === Number(mensagem.id)
            );

            if (!existe) {
                this.mensagens.push(mensagem);
            }

            const conversa = this.conversas.find(
                item => Number(item.chat_id) === Number(mensagem.chat_id)
            );

            if (conversa) {
                conversa.ultima_mensagem = mensagem.conteudo;
                conversa.ultima_mensagem_usuario = mensagem.usuario_id;
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
                    `/chat/${this.conversaSelecionada.negociacao_id}/mensagens`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ conteudo })
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
            this.conversaSelecionada = null;
            this.mensagens = [];
            this.mensagemNova = '';
            this.erroMensagens = '';
            this.erroEnvio = '';
        },

        rolarHistorico() {
            const lista = this.$refs.listaMensagens;

            if (lista) {
                lista.scrollTop = lista.scrollHeight;
            }
        },

        formatarDataHora(data) {
            if (!data) return '';

            let dataMensagem;

            const brasileira = String(data).match(
                /^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/
            );

            if (brasileira) {
                dataMensagem = new Date(
                    Number(brasileira[3]),
                    Number(brasileira[2]) - 1,
                    Number(brasileira[1]),
                    Number(brasileira[4]),
                    Number(brasileira[5])
                );
            } else {
                dataMensagem = new Date(data);
            }

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
