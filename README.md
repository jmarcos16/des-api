### Market app

API para cadastro de produtos e categorias.

### Requisitos
- PHP 8.0 ou superior
- Composer
- PostgreSQL

### Instalação
1. Clone o repositório

```bash
git clone https://github.com/jmarcos16/des-api.git && cd des-api
```

2. Instale as dependências

```bash
composer install
```

3. Copie o arquivo `.env.example` para `.env` e configure o banco de dados

4. Altere os seguintes campos no arquivo `.env` com as configurações do seu banco de dados.

```bash
DB_CONNECTION=pgsql
DB_HOST=localhost # Host do banco de dados
DB_PORT=5432  # Porta do banco de dados
DB_DATABASE=postgres  # Nome do banco de dados
DB_USERNAME=postgres # Usuário do banco de dados
DB_PASSWORD=secret  # Senha do banco de dados
```
Lembre-se de alterar o usuário e o nome do banco de dados de acordo com as configurações do seu ambiente.

5. O backup do banco de dados está disponível no arquivo `postgres_dump.backup` na raiz do projeto. Para restaurar o banco de dados execute o comando abaixo:

```bash
pg_restore -U postgres -d postgres -v "postgres_dump.backup"

```
O nome do banco de dados é `postgres` e o usuário é `postgres`. Caso você tenha alterado o nome do banco de dados ou o usuário, altere o comando acima de acordo com as configurações do seu ambiente.

6. Inicie o servidor

```bash
php -S localhost:8080
```

7. Você pode baixar o frontend do projeto [aqui](https://github.com/jmarcos16/des-front) e seguir as instruções para instalação.

### Rotas

#### Produtos
- GET /products
- POST /products
- GET /products/all
#### Tipos de produtos
- GET /product-types
- GET /product-types/all
- POST /product-types
#### Vendas
- GET /sales
- POST /sales

### Testes
Para rodar os testes execute o comando abaixo:

```bash
./vendor/bin/phpunit 
# ou
composer test
```
