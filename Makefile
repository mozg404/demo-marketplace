init: sail-down-clear sail-update sail-up storage-link init-npm migrate-fresh scout-flush reset seed
up: sail-up
down: sail-down
restart: down up
refresh: sail-down-clear sail-up migrate-fresh scout-flush clear seed

# --------------------------
# Инсталляция
# --------------------------

init-npm:
	./vendor/bin/sail npm install

regenerate-key:
	./vendor/bin/sail php artisan key:generate

# --------------------------
# Sail
# --------------------------

sail-up:
	./vendor/bin/sail up -d

sail-down:
	./vendor/bin/sail down --remove-orphans

sail-down-clear:
	./vendor/bin/sail down -v --remove-orphans

sail-update:
	./vendor/bin/sail pull pgsql mailpit meilisearch
	./vendor/bin/sail build --no-cache

# --------------------------
# Миграции и данные
# --------------------------

migrate:
	./vendor/bin/sail php artisan migrate

migrate-fresh:
	sleep 2
	./vendor/bin/sail php artisan migrate:fresh

migrate-refresh:
	./vendor/bin/sail php artisan migrate:refresh

seed:
	sleep 2
	./vendor/bin/sail php artisan db:seed

scout-flush:
	@echo "Очистка Meilisearch..."
	./vendor/bin/sail artisan scout:flush "App\Models\Product"

scout-import:
	@echo "Импорт в Meilisearch..."
	./vendor/bin/sail artisan scout:import "App\Models\Product"

# --------------------------
# Фронтенд
# --------------------------

frontend:
	./vendor/bin/sail npm run dev

# --------------------------
# Утилиты
# --------------------------

schedule:
	./vendor/bin/sail php artisan schedule:work

queue:
	./vendor/bin/sail php artisan queue:work --queue=high,default

project-init:
	./vendor/bin/sail php artisan project:init

generate-phpdoc:
	./vendor/bin/sail php artisan ide-helper:generate
	./vendor/bin/sail php artisan ide-helper:models -RW

test:
	./vendor/bin/sail php artisan test

storage-link:
	./vendor/bin/sail php artisan storage:link

# --------------------------
# Система очистки
# --------------------------
reset: clear clear-logs clear-media
clear: clear-cache-laravel clear-cache-debugbar clear-cache-purifier

clear-cache-laravel:
	./vendor/bin/sail php artisan cache:clear
	./vendor/bin/sail php artisan view:clear
	./vendor/bin/sail php artisan route:clear
	./vendor/bin/sail php artisan config:clear
	./vendor/bin/sail php artisan event:clear

clear-cache-debugbar:
	./vendor/bin/sail php artisan debugbar:clear

clear-cache-purifier:
	rm -rf storage/purifier

clear-logs:
	./vendor/bin/sail php artisan log:clear

clear-public-storage:
	find storage/app/public/* -type f -not -name ".gitignore" -delete 2>/dev/null || true
	find storage/app/public/* -type d -not -name ".gitignore" -exec rm -rf {} + 2>/dev/null || true

clear-media:
	./vendor/bin/sail php artisan media-library:clean

# --------------------------
# Подакшн
# --------------------------

prod: prod-down-clear prod-build prod-up prod-migrate prod-seed

prod-up:
	docker compose -f docker-compose.prod.yml --env-file .env.production up -d

prod-down:
	docker compose -f docker-compose.prod.yml --env-file .env.production down

prod-down-clear:
	docker compose -f docker-compose.prod.yml --env-file .env.production down -v --remove-orphans

prod-build:
	docker compose -f docker-compose.prod.yml --env-file .env.production build

prod-migrate:
	docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan migrate --force

prod-seed:
	docker compose -f docker-compose.prod.yml --env-file .env.production exec app php artisan db:seed --force

build:
	docker compose -f docker-compose.prod.yml --env-file .env.production build