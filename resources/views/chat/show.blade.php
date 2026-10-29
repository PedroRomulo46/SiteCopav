
@extends('layouts.layout')

@section('conteudo')
<div class="max-w-5xl mx-auto px-3 sm:px-5 py-5 sm:py-8">

    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabeçalho --}}
        <header class="bg-[#1B4D3E] text-white px-4 sm:px-7 py-5">
            <a href="{{ route('negociacoes.show', $negociacao) }}"
               class="inline-flex items-center gap-2 text-sm text-white/75 hover:text-white transition">
                <span aria-hidden="true">←</span>
                Voltar à negociação
            </a>

            <div class="flex items-center gap-3 mt-5">
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15
                            flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">forum</span>
                </div>

                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold">
                        Conversa da negociação #{{ $negociacao->id }}
                    </h1>
                    <p class="text-sm text-white/75 mt-1">
                        {{ $negociacao->cliente->nome ?? 'Cliente' }}
                        <span class="mx-1">·</span>
                        {{ $negociacao->oferta->fornecedor->nome ?? 'Fornecedor' }}
                    </p>
                </div>
            </div>
        </header>

        {{-- Aviso --}}
        <div class="px-4 sm:px-7 py-3 bg-[#F3F7F4] border-b border-gray-100
                    flex items-start gap-2">
            <span class="material-symbols-outlined text-[#236350] text-xl">verified_user</span>
            <p class="text-xs sm:text-sm text-gray-600">
                Utilize este espaço para combinar detalhes do produto, valores e entrega.
            </p>
        </div>

        {{-- Histórico --}}
        <div id="lista-mensagens"
             data-chat-id="{{ $chat->id }}"
             class="px-4 sm:px-7 py-6 space-y-5 bg-[#F8FAF9]
                    min-h-[350px] h-[52vh] max-h-[600px] overflow-y-auto">

            @forelse ($chat->mensagens as $mensagem)
                @php
                    $minhaMensagem = (int) $mensagem->usuario_id === (int) auth()->id();
                @endphp

                <div class="flex {{ $minhaMensagem ? 'justify-end' : 'justify-start' }}"
                     data-message-id="{{ $mensagem->id }}">

                    <div class="max-w-[88%] sm:max-w-[75%] rounded-2xl px-4 py-3 shadow-sm
                        {{ $minhaMensagem
                            ? 'bg-[#236350] text-white rounded-br-md'
                            : 'bg-white border border-gray-200 text-gray-800 rounded-bl-md' }}">

                        <p class="text-xs font-semibold mb-1
                            {{ $minhaMensagem ? 'text-white/75' : 'text-[#236350]' }}">
                            {{ $minhaMensagem ? 'Você' : ($mensagem->usuario->nome ?? 'Participante') }}
                        </p>

                        <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $mensagem->conteudo }}</p>

                        <p class="text-[11px] mt-2 text-right
                            {{ $minhaMensagem ? 'text-white/70' : 'text-gray-400' }}">
                            {{ $mensagem->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            @empty
                <div id="mensagens-vazias" class="text-center py-14">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-[#E5EFEA]
                                flex items-center justify-center text-[#236350]">
                        <span class="material-symbols-outlined text-3xl">chat</span>
                    </div>
                    <h2 class="font-semibold text-gray-800 mt-4">
                        Sua conversa começa aqui
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Envie uma mensagem para iniciar a conversa.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Formulário de envio --}}
        <form id="form-mensagem" class="p-4 sm:px-7 sm:py-5 bg-white border-t border-gray-200">
            @csrf

            <label for="conteudo-mensagem"
                   class="block text-sm font-semibold text-gray-700 mb-2">
                Sua mensagem
            </label>

            <div class="flex items-end gap-2 sm:gap-3">
                <textarea
                    id="conteudo-mensagem"
                    name="conteudo"
                    rows="2"
                    maxlength="5000"
                    placeholder="Escreva sua mensagem..."
                    class="flex-1 min-w-0 resize-none rounded-xl border border-gray-300
                           bg-gray-50 px-4 py-3 text-sm text-gray-800
                           placeholder:text-gray-400 outline-none
                           focus:border-[#236350] focus:ring-2 focus:ring-[#236350]/15
                           transition"
                    required
                ></textarea>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl
                           bg-[#236350] hover:bg-[#1B4D3E] text-white
                           font-semibold px-4 sm:px-5 py-3 min-h-[48px]
                           transition disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span id="texto-botao">Enviar</span>
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </div>

            <div class="flex flex-wrap justify-between gap-2 mt-3">
                <p id="estado-chat" class="text-xs text-gray-400">
                    Suas mensagens serão salvas nesta conversa.
                </p>
                <p class="text-xs text-gray-400">Máximo: 5000 caracteres</p>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('form-mensagem');
    const campo = document.getElementById('conteudo-mensagem');
    const lista = document.getElementById('lista-mensagens');
    const botao = formulario.querySelector('button[type="submit"]');
    const textoBotao = document.getElementById('texto-botao');
    const estado = document.getElementById('estado-chat');

    const chatId = lista.dataset.chatId;
    const usuarioId = {{ (int) auth()->id() }};
    const urlEnvio = @json(route('chat.mensagens.store', $negociacao));
    const token = formulario.querySelector('input[name="_token"]').value;

    function adicionarMensagem(mensagem) {
        // Evita duplicar mensagens já exibidas.
        if (lista.querySelector(`[data-message-id="${mensagem.id}"]`)) {
            return;
        }

        document.getElementById('mensagens-vazias')?.remove();

        const minhaMensagem = Number(mensagem.usuario_id) === usuarioId;
        const linha = document.createElement('div');
        linha.dataset.messageId = mensagem.id;
        linha.className = `flex ${minhaMensagem ? 'justify-end' : 'justify-start'}`;

        const balao = document.createElement('div');
        balao.className = 'max-w-[88%] sm:max-w-[75%] rounded-2xl px-4 py-3 shadow-sm ' +
            (minhaMensagem
                ? 'bg-[#236350] text-white rounded-br-md'
                : 'bg-white border border-gray-200 text-gray-800 rounded-bl-md');

        const autor = document.createElement('p');
        autor.className = 'text-xs font-semibold mb-1 ' +
            (minhaMensagem ? 'text-white/75' : 'text-[#236350]');
        autor.textContent = minhaMensagem ? 'Você' : (mensagem.nome || 'Participante');

        const texto = document.createElement('p');
        texto.className = 'whitespace-pre-wrap break-words text-sm leading-relaxed';
        texto.textContent = mensagem.conteudo;

        const horario = document.createElement('p');
        horario.className = 'text-[11px] mt-2 text-right ' +
            (minhaMensagem ? 'text-white/70' : 'text-gray-400');
        horario.textContent = mensagem.created_at;

        balao.append(autor, texto, horario);
        linha.appendChild(balao);
        lista.appendChild(linha);

        lista.scrollTop = lista.scrollHeight;
    }

    // Recebe mensagens novas pelo Laravel Echo.
    if (window.Echo) {
        window.Echo.private(`chat.${chatId}`)
            .listen('.mensagem.enviada', (mensagem) => {
                adicionarMensagem(mensagem);
            });
    } else {
        console.error('Laravel Echo não está disponível nesta página.');
        estado.textContent = 'Conexão em tempo real indisponível.';
    }

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        const conteudo = campo.value.trim();
        if (!conteudo) {
            campo.focus();
            return;
        }

        botao.disabled = true;
        textoBotao.textContent = 'Enviando...';
        estado.textContent = 'Enviando sua mensagem...';

        try {
            const resposta = await fetch(urlEnvio, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ conteudo }),
            });

            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(dados.message || 'Não foi possível enviar a mensagem.');
            }

            adicionarMensagem(dados.mensagem);
            campo.value = '';
            estado.textContent = 'Mensagem enviada.';
            campo.focus();

        } catch (erro) {
            console.error('Erro ao enviar mensagem:', erro);
            estado.textContent = erro.message || 'Erro ao enviar mensagem.';
        } finally {
            botao.disabled = false;
            textoBotao.textContent = 'Enviar';
            lista.scrollTop = lista.scrollHeight;
        }
    });

    // Permite enviar com Ctrl + Enter sem impedir que Enter crie outra linha.
    campo.addEventListener('keydown', (evento) => {
        if (evento.key === 'Enter' && (evento.ctrlKey || evento.metaKey)) {
            evento.preventDefault();
            formulario.requestSubmit();
        }
    });

    lista.scrollTop = lista.scrollHeight;
});
</script>
@endpush
