@extends('site.layout')
@section('title', 'home')
@section('conteudo')

<section class="p-5">
  <h1 class="text-xl font-bold mb-4">Produtos que você pode se interessar...</h1>
  <button class="btn btn-success text-white mb-4">Cadastrar Novo Lote +</button>

  <!-- Abas -->
  <div role="tablist" class="tabs tabs-bordered mb-4">
    <a role="tab" class="tab tab-active font-bold">Meus Lotes</a>
    <a role="tab" class="tab">Demandas da Empresa</a>
  </div>

  <!-- Lista de itens -->
  <div class="bg-green-400 w-full p-4 flex flex-col gap-4">
    <!-- Card 1 -->
    <div class="card card-side bg-base-100 w-full max-w-2xl shadow-sm overflow-hidden">
      <figure class="w-1/3 shrink-0">
        <img class="w-full h-full object-cover" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Milho" />
      </figure>
      <div class="card-body p-5"> 
        <h2 class="card-title text-lg"><strong>Lote [10]:</strong> 5 Ton. Milho</h2>
        <p class="text-sm"><strong>Status:</strong> Em estoque</p>
        <p class="text-sm">Ofertado P/Empresa</p>
        <div class="card-actions justify-end mt-2">
          <button class="btn btn-primary btn-sm">Ver detalhes</button>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
      <div class="card card-side bg-base-100 w-full max-w-2xl shadow-sm overflow-hidden">
        <figure class="w-1/3 shrink-0">
          <img class="w-full h-full object-cover" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Milho" />
        </figure>
        <div class="card-body p-5"> 
          <h2 class="card-title text-lg"><strong>Lote [10]:</strong> 5 Ton. Milho</h2>
          <p class="text-sm"><strong>Status:</strong> Em estoque</p>
          <p class="text-sm">Ofertado P/Empresa</p>
          <div class="card-actions justify-end mt-2">
            <button class="btn btn-primary btn-sm">Ver detalhes</button>
          </div>
        </div>
      </div>

  </div>
</section>

@endsection