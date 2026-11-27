@extends('layouts.layout')

@section('title', 'Mapa de Fornecedores')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .mapa-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: start;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .mapa-grid {
                grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
            }
        }

        #mapa-fornecedores {
            width: 100%;
            height: 540px;
            z-index: 1;
        }

        html.tema-escuro .leaflet-tile-pane,
        html[data-theme="dark"] .leaflet-tile-pane {
            filter: brightness(0.82) contrast(1.08) saturate(0.85);
        }
    </style>
@endpush

@section('conteudo')

@php
    $comCoordenadas = collect($marcadores)->filter(fn ($m) => !is_null($m['lat']) && !is_null($m['lng']))->values();
@endphp

<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="w-full max-w-7xl mx-auto space-y-6 text-left">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Mapa de Fornecedores
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Visualização restrita a administradores com os fornecedores cadastrados na plataforma.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-sm shrink-0">
            <span class="material-symbols-outlined text-base text-[#236350] dark:text-emerald-400 leading-none">location_on</span>
            <span>{{ $comCoordenadas->count() }} mapeados de {{ count($marcadores) }} fornecedores</span>
        </div>
    </div>

    <div class="mapa-grid">
        {{-- Mapa --}}
        <div class="w-full bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm overflow-hidden">
            <div id="mapa-fornecedores"></div>
        </div>

        {{-- Lista lateral --}}
        <div class="w-full bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm p-4 space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200">
                Fornecedores ({{ count($marcadores) }})
            </h2>

            <div class="space-y-2.5 max-h-470px overflow-y-auto pr-1">
                @forelse($marcadores as $m)
                    @php $temCoord = !is_null($m['lat']) && !is_null($m['lng']); @endphp
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">{{ $m['nome'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $m['cidade'] ?? 'Cidade não informada' }}{{ ($m['cidade'] && $m['estado']) ? ' - ' : '' }}{{ $m['estado'] ?? '' }}
                            </p>
                            @unless($temCoord)
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                                    Endereço não localizado no mapa
                                </p>
                            @endunless
                        </div>

                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            @if($temCoord)
                                <button
                                    type="button"
                                    onclick="focarFornecedor({{ $m['lat'] }}, {{ $m['lng'] }}, {{ $m['id'] }})"
                                    class="text-xs font-semibold text-[#236350] dark:text-emerald-400 hover:underline cursor-pointer"
                                >
                                    Ver no mapa
                                </button>
                            @endif
                            <a
                                href="{{ $m['url'] }}"
                                class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                            >
                                Perfil
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400 py-6 text-center">
                        Nenhum fornecedor cadastrado.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        let mapaInstancia = null;
        const markersPorId = {};

        document.addEventListener('DOMContentLoaded', function () {
            const fornecedores = @json($comCoordenadas);
            const centroInicial = fornecedores.length > 0
                ? [fornecedores[0].lat, fornecedores[0].lng]
                : [-5.1786, -37.3522]; // Próximo a Icapuí / litoral CE-RN caso não haja marcadores

            const zoomInicial = fornecedores.length > 0 ? 11 : 7;

            mapaInstancia = L.map('mapa-fornecedores').setView(centroInicial, zoomInicial);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapaInstancia);

            const bounds = [];

            fornecedores.forEach(function (f) {
                const pos = [f.lat, f.lng];
                bounds.push(pos);

                const popupHtml = `
                    <div style="min-width: 180px;">
                        <strong>${f.nome}</strong><br>
                        <small>${[f.endereco, f.cidade, f.estado].filter(Boolean).join(' - ')}</small><br>
                        <a href="${f.url}" style="display:inline-block;margin-top:6px;font-weight:600;color:#047857;">Abrir perfil</a>
                    </div>
                `;

                const marker = L.marker(pos).addTo(mapaInstancia).bindPopup(popupHtml);
                markersPorId[f.id] = marker;
            });

            if (bounds.length > 1) {
                mapaInstancia.fitBounds(bounds, { padding: [30, 30] });
            }

            setTimeout(() => {
                mapaInstancia.invalidateSize();
            }, 150);
        });

        function focarFornecedor(lat, lng, id) {
            if (!mapaInstancia) return;
            mapaInstancia.setView([lat, lng], 14, { animate: true });
            if (markersPorId[id]) {
                markersPorId[id].openPopup();
            }
        }
    </script>
@endpush
@endsection