up: #create and start containers
	@docker compose up -d


down: #stop containers
	@docker compose down


rebuild: #rebuild all
	@docker compose down && docker compose up -d --build

# Start Go AI microservice container
up-go-ai:
	docker-compose up -d grpc-ai

connect_back: #connect to php application
	@docker exec -it php-fpm bash

install: install-php install-frontend

# Generate gRPC PHP classes inside the php-fpm container
generate-grpc:
	docker-compose exec php-fpm protoc \
        --proto_path=/var/www/backend/ai_service \
        --php_out=/var/www/backend/src/Service \
        /var/www/backend/ai_service/ai_service.proto

setup:  ## Create and start containers
	$(MAKE) up-redis
	$(MAKE) install
	$(MAKE) up-php
	$(MAKE) up-frontend
	$(MAKE) up-go-ai
	$(MAKE) up-nginx
	$(MAKE) generate-grpc

up-mysql: ## Create and start containers
	docker compose up -d mysql

up-redis: ## Create and start containers
	docker compose up -d redis

up-frontend: ## Start panel-ui
	docker compose up --build  -d frontend

up-nginx:
	docker compose up -d --no-deps --build nginx

install-php: ## install-php
	ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts
	docker compose build php-fpm
	docker compose run --rm --no-deps php-fpm composer install


install-frontend: ## Install dependecy before run in watcher mode
	docker compose build frontend
	docker compose run --rm --no-deps frontend yarn install

up-php: ## Create and start containers
	docker-compose up -d php-fpm

test:
	docker-compose exec php-fpm vendor/bin/phpunit --configuration phpunit.xml