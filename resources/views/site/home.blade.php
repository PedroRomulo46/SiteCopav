@extends('site.layout')
@section('title', 'Home')

@section('conteudo')

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
  [x-cloak] { display: none !important; }
</style>

<div x-data="{ abaAtiva: 'demandas' }" class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-gray-50 min-h-screen">

  <!-- Coluna Esquerda: Ações e Gestão (Lotes/Demandas) -->
  <div class="lg:col-span-5 h-fit p-4 rounded-2xl flex flex-col gap-4" style="background-color: #DDD8CC;">
    
    <!-- Botão Novo Lote -->
    {{-- TODO (BACKEND): Substituir href por {{ route('lotes.create') }} --}}
    <a href="/lotes/cadastrar" class="w-full">
      <button class="btn text-white bg-[#79A961] hover:bg-[#709b58] w-full border-none text-lg py-8 rounded-xl shadow-inner">
        Cadastrar novo lote +
      </button>
    </a>

    <!-- Painel de Abas e Listagem -->
    <div class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md">

      <!-- Navegação de Abas -->
      <div role="tablist" class="tabs tabs-border w-full flex justify-around border-b pb-2">
        <button
          @click="abaAtiva = 'lotes'"
          :class="{ 'tab-active font-bold text-gray-800': abaAtiva === 'lotes', 'text-gray-500': abaAtiva !== 'lotes' }"
          class="tab transition-all">
          Meus Lotes
        </button>
        <button
          @click="abaAtiva = 'demandas'"
          :class="{ 'tab-active font-bold text-gray-800': abaAtiva === 'demandas', 'text-gray-500': abaAtiva !== 'demandas' }"
          class="tab transition-all">
          Demandas da Empresa
        </button>
      </div>

      <!-- Lista Dinâmica (Lotes / Demandas) -->
      <div class="flex flex-col gap-3 mt-2">
        
        {{-- TODO (BACKEND): Criar @foreach($demandas as $demanda) ou condicional com $lotes --}}
        
        <!-- Item 1 (Mock) -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/milho.png') }}" alt="Milho" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #5]: 5 Ton. Milho</h3>
              <p class="text-gray-500">Expira em: 24/09/2026</p>
            </div>
            <div class="flex justify-end mt-2">
              {{-- TODO (BACKEND): Linkar com rota de exibição de demanda --}}
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961;">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 2 (Mock) -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/caju.png') }}" alt="Caju" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #6]: 3 Ton. Caju</h3>
              <p class="text-gray-500">Expira em: 30/05/2027</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961;">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 3 (Mock) -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/feijao.png') }}" alt="Feijão" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #7]: 2 Ton. Feijão</h3>
              <p class="text-gray-500">Expira em: 07/12/2026</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961;">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

        <!-- Item 4 (Mock) -->
        <div class="flex items-start gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:shadow-[0_0_8px_3px_rgba(0,0,0,0.2)] transition-shadow">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/tomate.png') }}" alt="Tomate" />
          <div class="flex flex-col justify-between grow self-stretch text-xs">
            <div>
              <h3 class="font-bold text-gray-800 text-sm">[Demanda #8]: 1 Ton. Tomate</h3>
              <p class="text-gray-500">Expira em: 12/02/2027</p>
            </div>
            <div class="flex justify-end mt-2">
              <button class="btn border-2 hover:bg-[#79A961] hover:text-white p-2 btn-xs sm:btn-sm" style="border-color: #79A961;">
                Ver detalhes
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Coluna Direita: Vitrine de Produtos -->
  <div class="lg:col-span-7 flex flex-col gap-4 items-center">
    <h1 class="text-xl font-bold text-gray-800 self-start">Produtos que você pode se interessar...</h1>

    <!-- Grid de Cards de Produtos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 w-full">
      
      {{-- 
        TODO (BACKEND): Substituir estes cards estáticos por um @foreach($produtos as $produto)
        Exemplo de estrutura:
        <a href="{{ route('produtos.show', $produto->id) }}">
          <div class="...">
            <img src="{{ asset('storage/' . $produto->imagem) }}" ... />
            <h2>{{ $produto->nome }}</h2>
            <p>R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
          </div>
        </a>
      --}}

      <!-- Produto 1 (Mock) -->
      <a href="/produtos/detalhes" class="block">
        <div class="bg-white hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.2)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all">
          <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/sementesmilho.png') }}" alt="Sementes Milho" />
          <div>
            <h2 class="font-bold text-gray-800">Sementes Milho Híbrido</h2>
            <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
            <p class="text-lg font-bold text-green-600 mt-1">R$ 45,00/Kg</p>
            <span class="text-xs text-gray-400">Hugo Jorge</span>
          </div>
        </div>
      </a>

      <!-- Produto 2 (Mock) -->
      <a href="/produtos/detalhes" class="block">
        <div class="bg-white hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.2)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all">
          <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/drone.png') }}" alt="Drone" />
          <div>
            <h2 class="font-bold text-gray-800">Drone Pulverizador</h2>
            <p class="text-xs text-gray-400">Ideal para a pulverização de grandes campos</p>
            <p class="text-lg font-bold text-green-600 mt-1">R$ 60.000,00</p>
            <span class="text-xs text-gray-400">Hugo Jorge</span>
          </div>
        </div>
      </a>

      <!-- Produto 3 (Mock) -->
      <a href="/produtos/detalhes" class="block">
        <div class="bg-white hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.2)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all">
          <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/nutriente.png') }}" alt="Nutriente de solo" />
          <div>
            <h2 class="font-bold text-gray-800">Nutriente de solo</h2>
            <p class="text-xs text-gray-400">Ideal para o crescimento e frutificação</p>
            <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
            <span class="text-xs text-gray-400">Hugo Jorge</span>
          </div>
        </div>
      </a>

      <!-- Produto 4 (Mock) -->
      <a href="/produtos/detalhes" class="block">
        <div class="bg-white hover:shadow-[0_0_20px_2px_rgba(0,0,0,0.2)] p-3 rounded-2xl flex flex-col justify-between h-full transition-all">
          <img class="w-full h-64 object-cover rounded-xl mb-3" src="{{ asset('assets/maca.png') }}" alt="Maçã Fuji" />
          <div>
            <h2 class="font-bold text-gray-800">Maçã Fuji</h2>
            <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
            <p class="text-lg font-bold text-green-600 mt-1">R$ 12,00/Kg</p>
            <span class="text-xs text-gray-400">Hugo Jorge</span>
          </div>
        </div>
      </a>

    </div>
  </div>

</div>

@endsection