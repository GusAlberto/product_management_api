# product_management_api

## Project Architecture
```bash
/home/engineer/Projetos/product_management_api/
├── app/
├── bootstrap/
├── config/
├── database/
├── routes/
├── tests/
├── artisan
├── composer.json
├── .env
├── README.md
└── ... (arquivos Laravel)
```

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

		```bash
		touch database/database.sqlite
		php artisan migrate
		```

	- Run tests (uses the same DB file unless you override):

		```bash
		php artisan test --filter ProductApiTest
		```
---

# **Suporte MySQL Opcional**: 
Há um arquivo `docker-compose.yml` de conveniência fornecido se você quiser executar uma instância MySQL local com as credenciais padrão.

	- Inicie o MySQL com Docker (ou use o comando `docker run` mostrado abaixo):

		```bash
		# usando docker run (funciona sem o plugin docker-compose)
		docker run -d --name desafio_produtos_db \
			-e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=desafio_produtos \
			-p 3306:3306 -v "$PWD/mysql_data":/var/lib/mysql mysql:8.0

		# ou se você usar docker compose: docker compose up -d
		```

	- Para usar MySQL, atualize `.env` (ou crie um `.env.testing`) com:

		```env
		DB_CONNECTION=mysql
		DB_HOST=127.0.0.1
		DB_PORT=3306
		DB_DATABASE=desafio_produtos
		DB_USERNAME=root
		DB_PASSWORD=root
		```

	- Execute as migrações contra MySQL e rode os testes:

		```bash
		php artisan migrate
		php artisan test --filter ProductApiTest
		```

