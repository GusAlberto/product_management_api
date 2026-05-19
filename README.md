# product_management_api
# README.md

## Requisitos
- PHP 8.2+
- Composer
- Laravel 11
- Banco relacional (MySQL/PostgreSQL/SQLite)

## Instalação
1. Clone o repositório
2. Execute: composer install
3. Copie .env.example para .env
4. Configure o banco
5. Execute: php artisan key:generate
6. Execute: php artisan migrate
7. Execute: php artisan serve

## Rodando os testes
php artisan test

## Endpoints
- GET /api/products
- POST /api/products
- GET /api/products/{id}
- PUT /api/products/{id}
- DELETE /api/products/{id}

## Filtros
Exemplo:
GET /api/products?name=mouse&min_price=50&max_price=200&min_stock=1&page=1&per_page=10
Collection mínima do Insomnia:

GET /api/products

POST /api/products

GET /api/products/{id}

PUT /api/products/{id}

DELETE /api/products/{id}

Exemplo de body para POST/PUT:

json
{
  "name": "Mouse Gamer",
  "description": "Mouse com 6 botões",
  "price": 199.90,
  "stock": 25
}

---
# Verificar se PHP tem SQLite
php -m | grep -i sqlite

# Rodar migrations (para garantir DB sqlite populado)
php artisan migrate

# Rodar o teste específico
php artisan test --filter ProductApiTest