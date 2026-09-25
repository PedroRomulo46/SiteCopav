@extends('layouts.layout')
@section('title', 'Home')

@section('conteudo')

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
  [x-cloak] { display: none !important; }
</style>

<div x-data="{ abaAtiva: 'lotes' }" class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-[#ebeae7] min-h-screen">

  <!-- Coluna Esquerda: Ações e Gestão -->
  <div class="lg:col-span-5 h-fit p-4 rounded-2xl flex flex-col gap-4" style="background-color: #123228;">
    
    @auth
      <a href="{{ route('ofertas.create') }}" class="w-full">
        <button class="btn text-white bg-[#236350] hover:bg-[#1B4D3E] w-full border-none text-lg py-8 rounded-xl shadow-inner">
          Cadastrar Nova Oferta +
        </button>
      </a>
    @endauth

    @guest
    <div class="bg-white p-6 rounded-xl text-center flex flex-col gap-3 shadow-md">
      <h2 class="font-bold text-gray-800 text-base">Quer vender no marketplace?</h2>
      <p class="text-xs text-gray-600">Acesse sua conta ou cadastre-se para criar lotes e enviar propostas.</p>
      <div class="flex gap-2 justify-center mt-2">
        <a href="{{ route('login') }}" class="btn bg-[#236350] hover:bg-[#1B4D3E] text-white btn-sm px-4 rounded-md">Entrar</a>
        <a href="{{ route('register') }}" class="btn btn-outline border-gray-400 text-gray-700 hover:bg-gray-100 btn-sm px-4 rounded-md">Cadastrar</a>
      </div>
    </div>
    @endguest

    @auth
    <div class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md">
      <div role="tablist" class="tabs tabs-border w-full flex justify-around border-b pb-2">
        <button
          @click="abaAtiva = 'lotes'"
          :class="{ 'tab-active font-bold text-gray-800': abaAtiva === 'lotes', 'text-gray-500': abaAtiva !== 'lotes' }"
          class="tab transition-all pb-1">
          Meus Lotes
        </button>
        <button
          @click="abaAtiva = 'demandas'"
          :class="{ 'tab-active font-bold text-gray-800': abaAtiva === 'demandas', 'text-gray-500': abaAtiva !== 'demandas' }"
          class="tab transition-all pb-1">
          Demandas da Empresa
        </button>
      </div>

      <!-- Aba 1: Meus Lotes -->
      <div x-show="abaAtiva === 'lotes'" class="flex flex-col gap-3 mt-2">
        @forelse($ofertas as $oferta)
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.15)] transition-shadow">
          <img
              class="w-20 h-20 rounded-lg object-cover shrink-0"
              src="{{ $oferta->produto->imagem
                  ? asset('storage/' . $oferta->produto->imagem)
                  : asset('assets/milho.png') }}"
              alt="{{ $oferta->produto->nome ?? 'Produto' }}"
          />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Lote #{{ $oferta->id }}]: {{ $oferta->quantidade }} {{ $oferta->unidade }} {{ $oferta->produto->nome ?? '' }}</h3>
              <p class="text-gray-500">
                Expira em: {{ $oferta->data_validade ? \Carbon\Carbon::parse($oferta->data_validade)->format('d/m/Y') : 'Sem data' }}
              </p>
            </div>
            <div class="flex justify-end mt-2">
              <a href="{{ route('ofertas.show', $oferta->id) }}" class="btn bg-[#236350] hover:bg-[#1B4D3E] text-white p-2 btn-xs sm:btn-sm rounded-md">
                Ver detalhes
              </a>
            </div>
          </div>
        </div>
        @empty
          <p class="text-gray-500 text-sm text-center py-4">Você ainda não possui lotes cadastrados.</p>
        @endforelse
      </div>

      <!-- Aba 2: Demandas -->
      <div x-show="abaAtiva === 'demandas'" x-cloak class="flex flex-col gap-3 mt-2">
        @forelse($demandas ?? [] as $demanda)
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/milho.png') }}" alt="Demanda" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #{{ $demanda->id }}]: {{ $demanda->titulo ?? 'Solicitação de Compra' }}</h3>
              <p class="text-gray-500">Status: {{ $demanda->status ?? 'Aberta' }}</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn bg-[#236350] hover:bg-[#1B4D3E] text-white p-2 btn-xs sm:btn-sm rounded-md">
                Enviar Proposta
              </button>
            </div>
          </div>
        </div>
        @empty
          <p class="text-gray-500 text-sm text-center py-4">Nenhuma demanda corporativa aberta no momento.</p>
        @endforelse
      </div>
    </div>
    @endauth
  </div>

  <!-- Coluna Direita: Vitrine de Produtos -->
  <div class="lg:col-span-7 flex flex-col gap-4 items-center">
    <h1 class="text-xl font-bold text-gray-800 self-start">Produtos que você pode se interessar...</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 w-full">
    @forelse($produtos as $item)
      <a href="{{ route('ofertas.show', $item->id) }}" class="block h-full">
        <div class="bg-white shadow-md hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.15)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all border border-gray-100">
          <div>
            <img
                class="w-full h-48 object-cover rounded-xl mb-3"
                src="{{ $item->produto->imagem
                    ? asset('storage/' . $item->produto->imagem)
                    : asset('assets/milho.png') }}"
                alt="{{ $item->produto->nome ?? 'Produto' }}"
            />
            <h2 class="font-bold text-gray-800 line-clamp-1">{{ $item->produto->nome ?? 'Sem nome' }}</h2>
            <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $item->produto->descricao ?? '' }}</p>
          </div>
          <div class="mt-3 flex justify-between items-center">
            <span class="text-xs text-gray-500">Unidade: {{ $item->unidade }}</span>
            <span class="text-md font-bold text-[#79A961]">
              R$ {{ number_format($item->valor ?? 0, 2, ',', '.') }}
            </span>
          </div>
        </div>
      </a>
    @empty
      <p class="text-gray-500 col-span-full text-center py-6">Nenhum lote disponível no mercado.</p>
    @endforelse
    </div>
  </div>

</div>
@endsection