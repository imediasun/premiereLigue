up: #create and start containers
	@docker compose up -d


down: #stop containers
	@docker compose down


rebuild: #rebuild all
	@docker compose down && docker compose up -d --build


connect_back: #connect to laravel application
	@docker exec -it ]php-fpm bash

install: install-php install-frontend

setup:  ## Create and start containers
	$(MAKE) up-redis
	$(MAKE) install
	$(MAKE) up-php
	$(MAKE) up-frontend
	#$(MAKE) init
	$(MAKE) up-nginx

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
	docker compose up -d php-fpm