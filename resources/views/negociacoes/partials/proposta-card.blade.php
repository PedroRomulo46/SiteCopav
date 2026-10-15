
@php
    $valorTotal = $proposta->valor * ($proposta->quantidade ?? 0);
@endphp

<div
    class="relative p-6 hover:bg-gray-50/60 transition-colors {{ is_null($proposta->visualizada_em) ? 'bg-yellow-50/30' : '' }}"
    data-proposta-id="{{ $proposta->id }}"
>
    @if(
        is_null($proposta->visualizada_em) &&
        $proposta->status === 'pendente'
    )
        <span
            class="indicador-nova-proposta absolute top-4 right-4 w-2.5 h-2.5 rounded-full bg-yellow-400"
            title="Nova proposta"
        ></span>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

        <div class="md:col-span-4 flex items-center gap-3.5 text-left">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-gray-600">person</span>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 text-base">
                    {{ $proposta->cliente->nome ?? $proposta->usuario->nome ?? 'Usuário' }}
                </h3>

                <p class="text-sm text-gray-500 mt-0.5">
                    Negociação #{{ $proposta->negociacao_id }}
                </p>
            </div>
        </div>

        <div class="md:col-span-5 grid grid-cols-3 gap-2 text-left py-2 md:py-0">
            <div>
                <p class="text-xs text-gray-400 font-medium">Quantidade</p>
                <p class="font-bold text-gray-800 mt-0.5 text-sm">
                    @if($proposta->quantidade !== null)
                        {{ fmod($proposta->quantidade, 1) == 0
                            ? number_format($proposta->quantidade, 0, ',', '.')
                            : number_format($proposta->quantidade, 2, ',', '.') }}
                        <span class="text-xs font-normal text-gray-500">{{ $oferta->unidade }}</span>
                    @else
                        —
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Valor por unidade</p>
                <p class="font-bold text-[#236350] mt-0.5 text-sm">
                    R$ {{ number_format($proposta->valor, 2, ',', '.') }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Valor total</p>
                <p class="font-bold text-gray-800 mt-0.5 text-sm">
                    @if($proposta->quantidade !== null)
                        R$ {{ number_format($valorTotal, 2, ',', '.') }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>

        <div class="md:col-span-3 flex flex-col md:items-end justify-center gap-1.5 text-left md:text-right">
            @if($proposta->status === 'pendente')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-100 px-3 py-1 rounded-full w-fit">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Pendente
                </span>
            @elseif($proposta->status === 'aceita')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full w-fit">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Aceita
                </span>
            @elseif($proposta->status === 'recusada')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full w-fit">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Recusada
                </span>
            @elseif($proposta->status === 'cancelada')
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 bg-gray-100 px-3 py-1 rounded-full w-fit">
                    Cancelada
                </span>
            @endif

            <a
                href="{{ route('propostas.visualizar', $proposta->id) }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#236350] hover:text-[#174c3e] transition-colors"
            >
                Ver negociação
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

    </div>
</div>
