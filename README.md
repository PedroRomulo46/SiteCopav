# Marketplace Agrícola - Copav

Sistema de marketplace voltado para o agronegócio no Vale do Jaguaribe, permitindo que produtores rurais negociem lotes, produtos e suprimentos de forma direta e ágil.

## Tecnologias Utilizadas

- **Backend:** Laravel 11 / PHP 8.3
- **Banco de Dados:** MySQL (Laragon)
- **Frontend:** Blade, Tailwind CSS (CDN), DaisyUI (CDN) e Google Material Symbols

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:
- [PHP](https://www.php.net/) (versão 8.2 ou superior)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (+ NPM)
- [Laragon](https://laragon.org/) ou outro ambiente de servidor MySQL local

## Passo a Passo para Instalação

Passo a passo para clonar e rodar o projeto em seu ambiente local:

### 1. Clonar o repositório
Abra o terminal e execute:
```bash
git clone [https://github.com/PedroRomulo46/SiteCopav](https://github.com/PedroRomulo46/SiteCopav)
cd SiteCopav
```

### 2. Instalar dependências do PHP
```bash
composer install
```

### 3. configurar as variáveis de ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. criar e configurar o banco de dados
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=root
DB_PASSWORD=
```

### 5. executar as migrações e popular o banco
```bash
php artisan migrate --seed
```

### 6. Gerar link para as imagens de produtos e perfis
```bash
npm install
npm run build
```

### 7. Instalar dependências front-end e gerar build
```bash
npm install
npm run build
```
