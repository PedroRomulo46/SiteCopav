# Marketplace Agrícola - Site Copav

Sistema de marketplace voltado para o agronegócio no Vale do Jaguaribe, permitindo que produtores rurais, cooperativas e clientes negociem lotes (`Oferta`), demandas (`Demanda`), propostas (`Proposta`) e realizem pagamentos e negociações de forma direta, segura e ágil.

## Tecnologias Utilizadas

- **Backend:** Laravel 13 / PHP 8.5
- **Banco de Dados:** MySQL (Laragon)
- **Tempo Real / WebSockets:** Laravel Reverb
- **Frontend:** Blade, Alpine.js, Tailwind CSS e Vite (`laravel-vite-plugin` / `@tailwindcss/vite`)
- **Processamento de Pagamentos:** Stripe Checkout (Modo de Teste)

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:
- [PHP](https://www.php.net/) (versão 8.2 ou superior)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (+ NPM)
- [Laragon](https://laragon.org/) ou outro ambiente de servidor MySQL local
- [Ngrok](https://ngrok.com/) (para expor o ambiente local e testar os webhooks de pagamento)

## Passo a Passo para Instalação

Passo a passo para clonar e rodar o projeto em seu ambiente local:

### 1. Clonar o repositório
```bash
git clone [https://github.com/PedroRomulo46/SiteCopav](https://github.com/PedroRomulo46/SiteCopav)
cd SiteCopav
```

2. Instalar dependências do PHP e do Stripe
```bash
composer install
composer require stripe/stripe-php
```

3. Configurar as variáveis de ambiente
```
cp .env.example .env
php artisan key:generate
```
3.1 Também configure a conexão com o banco no arquivo .env e a chave do stripe
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitecopav
DB_USERNAME=root
DB_PASSWORD=

STRIPE_SECRET=sk_test_sua_chave_secreta_aqui
```

4. Executar as migrações e popular o banco
```
php artisan migrate --seed
```

5. Gerar link para o storage
```
php artisan storage:link
```

6. Instalar dependências front-end
```
npm install
```

7. Executar o ambiente de desenvolvimento
```
composer run dev
```

8. Configuração de Webhooks (Stripe + Ngrok)
8.1 Inicie o Ngrok
```
ngrok http 80
```
8.2 Adicione um novo destino no painel de Webhooks no Stripe e adicione a URL pro seu destino
8.3 Selecione o evento checkout.session.completed e salve.
