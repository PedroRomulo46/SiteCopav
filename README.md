# Marketplace Agrícola - Copav

Sistema de marketplace voltado para o agronegócio no Vale do Jaguaribe, para produtores rurais negociarem lotes, produtos e suprimentos.

---

## Tecnologias Utilizadas

- Laravel 11 / PHP 8.3
- MySQL (Laragon)
- Blade, Tailwind CSS (CDN), DaisyUI (CDN) e Google Material Symbols

---

## Pré-requisitos

- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (+ NPM)
- [Laragon](https://laragon.org/) (servidor MySQL local)

---

## Passo a Passo para Configuração do Projeto

Siga os passos abaixo toda vez que fizer um novo clone do repositório:

### 1. Clonar o repositório
```bash
git clone <url-do-repositorio>
cd SiteCopav
```

### 2. Instalar dependências do PHP
```bash
composer install
```

### 3. Configurar as Variáveis de Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Criar e Configurar o Banco de Dados
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Executar as Migrações e Popular o Banco
```bash
php artisan migrate --seed
```

### 6. Instalar Dependências Front-end e Gerar Build
```bash
npm install
npm run build
```
