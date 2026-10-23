@extends('layouts.layout')

@section('conteudo')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="bg-[#1B4D3E] text-white px-6 py-4">
            <a
                href="{{ route('negociacoes.show', $negociacao) }}"
                class="text-sm text-white/80 hover:text-white"
            >
                ← Voltar à negociação
            </a>

            <h1 class="text-xl font-bold mt-2">
                Chat da negociação #{{ $negociacao->id }}
            </h1>

            <p class="text-sm text-white/80 mt-1">
                {{ $negociacao->cliente->nome ?? 'Cliente' }}
                ·
                {{ $negociacao->oferta->fornecedor->nome ?? 'Fornecedor' }}
            </p>
        </div>

        {{-- Histórico --}}
        <div
            id="lista-mensagens"
            class="p-6 space-y-4 bg-gray-50 min-h-[350px] max-h-[500px] overflow-y-auto"
        >
            @forelse ($chat->mensagens as $mensagem)
                @php
                    $minhaMensagem = (int) $mensagem->usuario_id === (int) auth()->id();
                @endphp

                <div class="flex {{ $minhaMensagem ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-3
                        {{ $minhaMensagem
                            ? 'bg-[#236350] text-white rounded-br-sm'
                            : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' }}">

                        <p class="text-xs font-semibold mb-1
                            {{ $minhaMensagem ? 'text-white/75' : 'text-gray-500' }}">
                            {{ $minhaMensagem ? 'Você' : ($mensagem->usuario->nome ?? 'Participante') }}
                        </p>

                        <p class="whitespace-pre-wrap break-words">{{ $mensagem->conteudo }}</p>

                        <p class="text-[11px] mt-2 text-right
                            {{ $minhaMensagem ? 'text-white/70' : 'text-gray-400' }}">
                            {{ $mensagem->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            @empty
                <p id="mensagens-vazias" class="text-center text-gray-500 py-16">
                    Nenhuma mensagem ainda. Inicie a conversa!
                </p>
            @endforelse
        </div>

        {{-- Formulário: envio será implementado na próxima etapa --}}
        <form id="form-mensagem" class="p-4 border-t border-gray-200 bg-white">
            @csrf

            <div class="flex items-end gap-3">
                <textarea
                    id="conteudo-mensagem"
                    name="conteudo"
                    rows="2"
                    maxlength="5000"
                    placeholder="Digite sua mensagem..."
                    class="textarea textarea-bordered w-full resize-y"
                    required
                ></textarea>

                <button
                    type="submit"
                    class="btn bg-[#236350] text-white hover:bg-[#1B4D3E]"
                >
                    Enviar
                </button>
            </div>

            <p class="text-xs text-gray-400 mt-2">
                Envie mensagens relacionadas à negociação.
            </p>
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

    const urlEnvio = @json(route('chat.mensagens.store', $negociacao));
    const usuarioId = {{ (int) auth()->id() }};
    const token = document.querySelector('meta[name="csrf-token"]')?.content
        || formulario.querySelector('input[name="_token"]').value;

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        const conteudo = campo.value.trim();

        if (!conteudo) {
            campo.focus();
            return;
        }

        botao.disabled = true;
        const textoOriginal = botao.textContent;
        botao.textContent = 'Enviando...';

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
                throw new Error(
                    dados.message || 'Não foi possível enviar a mensagem.'
                );
            }

            const mensagem = dados.mensagem;

            document.getElementById('mensagens-vazias')?.remove();

            const linha = document.createElement('div');
            linha.className = 'flex justify-end';

            const balao = document.createElement('div');
            balao.className =
                'max-w-[80%] rounded-2xl rounded-br-sm px-4 py-3 ' +
                'bg-[#236350] text-white';

            const autor = document.createElement('p');
            autor.className = 'text-xs font-semibold mb-1 text-white/75';
            autor.textContent = 'Você';

            const texto = document.createElement('p');
            texto.className = 'whitespace-pre-wrap break-words';
            texto.textContent = mensagem.conteudo;

            const horario = document.createElement('p');
            horario.className = 'text-[11px] mt-2 text-right text-white/70';
            horario.textContent = mensagem.created_at;

            balao.append(autor, texto, horario);
            linha.appendChild(balao);
            lista.appendChild(linha);

            campo.value = '';
            lista.scrollTop = lista.scrollHeight;
            campo.focus();

        } catch (erro) {
            alert(erro.message || 'Erro ao enviar a mensagem.');
        } finally {
            botao.disabled = false;
            botao.textContent = textoOriginal;
        }
    });

    lista.scrollTop = lista.scrollHeight;
});
</script>
@endpush