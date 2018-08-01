docker-up:
		docker-compose up -d

docker-down:
		docker-compose down

docker-build:
		docker-compose up --build -d

test:
		docker-compose exec php-fpm php ./bin/phpunit

grant-access:
		chmod -R 777 var

update-database:
		docker-compose exec php-fpm php bin/console doctrine:schema:update -f

clear-cache:
		docker-compose exec php-fpm php bin/console cache:clear --no-warmup -e prod

