<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FornecedorMapaController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        abort_unless($usuario && $usuario->isAdmin(), 403, 'Acesso restrito a administradores.');

        $fornecedores = Fornecedor::query()
            ->orderBy('nome')
            ->get();

        $marcadores = $fornecedores->map(function ($fornecedor) {
            $coords = $this->resolverCoordenadas($fornecedor);

            return [
                'id' => $fornecedor->id,
                'nome' => $fornecedor->nome ?? 'Fornecedor',
                'status' => $fornecedor->status ?? 'indefinido',
                'cidade' => $fornecedor->cidade,
                'estado' => $fornecedor->estado,
                'endereco' => $fornecedor->endereco,
                'telefone' => $fornecedor->telefone,
                'lat' => $coords['lat'] ?? null,
                'lng' => $coords['lng'] ?? null,
                'url' => route('fornecedores.show', $fornecedor),
            ];
        })->values();

        return view('admin.fornecedores.mapa', [
            'marcadores' => $marcadores,
        ]);
    }

    private function resolverCoordenadas(Fornecedor $fornecedor): ?array
    {
        if (! empty($fornecedor->latitude) && ! empty($fornecedor->longitude)) {
            return [
                'lat' => (float) $fornecedor->latitude,
                'lng' => (float) $fornecedor->longitude,
            ];
        }

        $consultas = [];

        if (! empty($fornecedor->endereco) && (! empty($fornecedor->cidade) || ! empty($fornecedor->estado))) {
            $consultas[] = implode(', ', array_filter([
                $fornecedor->endereco,
                $fornecedor->cidade,
                $fornecedor->estado,
                'Brasil',
            ]));
        }

        if (! empty($fornecedor->cidade) || ! empty($fornecedor->estado)) {
            $consultas[] = implode(', ', array_filter([
                $fornecedor->cidade,
                $fornecedor->estado,
                'Brasil',
            ]));
        }

        foreach ($consultas as $textoBusca) {
            $resultado = $this->buscarNoNominatim($textoBusca);
            if ($resultado !== null) {
                return $resultado;
            }
        }

        return null;
    }

    private function buscarNoNominatim(string $textoBusca): ?array
    {
        $cacheKey = 'osm_geocode_v2_' . md5(mb_strtolower(trim($textoBusca)));

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($textoBusca) {
            try {
                $resposta = Http::withHeaders([
                    'User-Agent' => 'SiteCopavMarketplace/1.0 (admin@sitecopav.com)',
                    'Accept-Language' => 'pt-BR,pt;q=0.9',
                ])->timeout(8)->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $textoBusca,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'br',
                ]);

                if ($resposta->successful()) {
                    $dados = $resposta->json();
                    if (! empty($dados[0]['lat']) && ! empty($dados[0]['lon'])) {
                        return [
                            'lat' => (float) $dados[0]['lat'],
                            'lng' => (float) $dados[0]['lon'],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return null;
        });
    }
}