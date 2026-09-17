@extends('site.layout')
@section('title', 'home')
@section('conteudo')

<div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 bg-gray-50 min-h-screen">

  <!-- Coluna esquerda: Lotes e Demandas -->
  <div class="lg:col-span-5 bg-green-600/80 p-4 rounded-2xl flex flex-col gap-4">
    
    <!-- Botão de cadastrar lotes -->
    <button class="btn bg-green-700 hover:bg-green-800 text-white w-full border-none text-lg py-3 rounded-xl shadow-inner">
      Cadastrar novo lote +
    </button>

    <!-- Card unificado em fundo branco -->
    <div class="bg-white rounded-xl p-4 flex flex-col gap-3 shadow-md">
      
      <!-- Abas -->
      <div role="tablist" class="tabs tabs-border w-full flex justify-around border-b pb-2">
        <a role="tab" class="tab text-gray-500">Meus Lotes</a>
        <a role="tab" class="tab tab-active font-bold text-gray-800">Demandas da empresa</a>
      </div>

      <!-- Lista de Demandas/Lotes -->
      <div class="flex flex-col gap-3 mt-2">
        
        <!-- Item 1 -->
        <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-gray-100 shadow-sm">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/milho.png') }}" alt="Milho" />
          <div class="text-xs">
            <h3 class="font-bold text-gray-800 text-sm">[Demanda #5]: 5 Ton. Milho</h3>
            <p class="text-gray-500">Expira em: 24/09/2026</p>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-gray-100 shadow-sm">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/caju.png') }}" alt="Caju" />
          <div class="text-xs">
            <h3 class="font-bold text-gray-800 text-sm">[Demanda #6]: 3 Ton. Caju</h3>
            <p class="text-gray-500">Expira em: 30/05/2027</p>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-gray-100 shadow-sm">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/feijao.png') }}" alt="Feijão" />
          <div class="text-xs">
            <h3 class="font-bold text-gray-800 text-sm">[Demanda #7]: 2 Ton. Feijão</h3>
            <p class="text-gray-500">Expira em: 07/12/2026</p>
          </div>
        </div>

        <!-- Item 4 -->
        <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-gray-100 shadow-sm">
          <img class="w-20 h-20 rounded-lg object-cover shrink-0" src="{{ asset('assets/tomate.png') }}" alt="Tomate" />
          <div class="text-xs">
            <h3 class="font-bold text-gray-800 text-sm">[Demanda #8]: 1 Ton. Tomate</h3>
            <p class="text-gray-500">Expira em: 12/02/2027</p>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Coluna direita: Produtos Recomendados -->
  <div class="lg:col-span-7 flex flex-col gap-4">
    <h1 class="text-xl font-bold text-gray-800">Produtos que você pode se interessar</h1>

    <!-- Grid 2x2 para os Cards de Produtos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      
      <!-- Produto 1 -->
      <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <img class="w-96 h-96 object-cover rounded-xl mb-3" src="{{ asset('assets/milho.png') }}" alt="Sementes Milho" />
        <div>
          <h2 class="font-bold text-gray-800">Sementes Milho Híbrido</h2>
          <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 2 -->
      <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <img class="w-96 h-96 object-cover rounded-xl mb-3" src="{{ asset('assets/caju.png') }}" alt="Sementes Milho" />
        <div>
          <h2 class="font-bold text-gray-800">Sementes Milho Híbrido</h2>
          <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 3 -->
      <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <img class="w-96 h-96 object-cover rounded-xl mb-3" src="{{ asset('assets/feijao.png') }}" alt="Nutriente de solo" />
        <div>
          <h2 class="font-bold text-gray-800">Nutriente de solo</h2>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
          <p class="text-xs text-gray-400">Para cultivar tomate</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

      <!-- Produto 4 -->
      <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <img class="w-96 h-96 object-cover rounded-xl mb-3" src="{{ asset('assets/tomate.png') }}" alt="Sementes Milho" />
        <div>
          <h2 class="font-bold text-gray-800">Sementes Milho Híbrido</h2>
          <p class="text-lg font-bold text-green-600 mt-1">R$ 450,00/Kg</p>
          <p class="text-xs text-gray-400">Alta produtividade - oferta especial</p>
          <span class="text-xs text-gray-400">Hugo Jorge</span>
        </div>
      </div>

    </div>
  </div>

</div>

@endsection