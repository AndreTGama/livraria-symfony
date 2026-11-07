.PHONY: setup up down test cache-clear

setup:
	docker compose up -d --build
	docker compose exec php composer install
	docker compose exec php chown -R www-data:www-data var
	docker compose exec php php bin/console doctrine:migrations:migrate -n
	docker compose exec php php bin/console doctrine:fixtures:load -n
	docker compose exec php php bin/console cache:warmup

up:
	docker compose up -d

down:
	docker compose down

test:
#Comando para dar permissão ao usuário 'user' para acessar a tabela livraria_test, desse modo, consigo rodar os testes pelo comando make test 
	docker compose exec database sh -c 'mysql -uroot -p"$$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS livraria_test; GRANT ALL ON livraria_test.* TO \"$$MYSQL_USER\"@\"%\";"'
	docker compose exec php php bin/console doctrine:database:create --env=test --if-not-exists
	docker compose exec php php bin/console doctrine:migrations:migrate --env=test -n
	docker compose exec php php bin/phpunit

cache-clear:
	docker compose exec php php bin/console cache:clear
	docker compose exec php php bin/console cache:pool:clear report.cache