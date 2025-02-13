.PHONY: up
up:
	docker-compose up -d

.PHONY: down
down:
	docker-compose down

.PHONY: rebuild
rebuild:
	docker-compose build --no-cache

.PHONY: install
install:
	docker exec kit_app npm install

.PHONY: bash
bash:
	docker exec -it kit_app bash
.PHONY: kit-sync
kit-sync:
	docker exec kit_app php artisan kit:sync-geography
