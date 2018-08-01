docker-up:
		docker-compose up -d

docker-down:
		docker-compose down

docker-build:
		docker-compose up --build -d

test:
		docker-compose exec php-fpm php ./bin/phpunit
