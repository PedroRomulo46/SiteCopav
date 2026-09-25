<h1>Fazer Oferta Direta</h1>

<p>
    Selecione uma demanda de uma empresa e informe sua oferta.
</p>

<form action="{{ route('ofertas-diretas.store') }}" method="POST">
    @csrf

    <div>
        <label>Demanda:</label>

        <select name="demanda_id" required>
            <option value="">Selecione uma demanda</option>

            @foreach($demandas as $demanda)
                <option
                    value="{{ $demanda->id }}"
                    {{ old('demanda_id') == $demanda->id ? 'selected' : '' }}
                >
                    {{ $demanda->nome_produto }}
                    -
                    {{ $demanda->cliente->nome }}
                    -
                    {{ $demanda->quantidade }} {{ $demanda->unidade }}
                </option>
            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Fornecedor:</label>

        <select name="fornecedor_id" required>
            <option value="">Selecione o fornecedor</option>

            @foreach($fornecedores as $fornecedor)
                <option
                    value="{{ $fornecedor->id }}"
                    {{ old('fornecedor_id') == $fornecedor->id ? 'selected' : '' }}
                >
                    {{ $fornecedor->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Quantidade que consegue fornecer:</label>

        <input
            type="number"
            name="quantidade"
            step="0.01"
            min="0"
            value="{{ old('quantidade') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Valor da oferta:</label>

        <input
            type="number"
            name="valor"
            step="0.01"
            min="0"
            value="{{ old('valor') }}"
            placeholder="Ex: 1150.00"
            required
        >
    </div>

    <br>

    <div>
        <label>Observação:</label>

        <textarea
            name="observacao"
            placeholder="Informe detalhes sobre sua oferta"
        >{{ old('observacao') }}</textarea>
    </div>

    <br>

    <button type="submit">
        Enviar oferta
    </button>
</form>

<br>

<a href="{{ route('demandas.index') }}">
    Ver demandas
</a>