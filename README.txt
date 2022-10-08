1. docker compose up -d
2. docker compose exec  php-apache bash
3. In the container: composer install
4. Application on http://localhost:8080/
5. if not starting because of permission error for laravel.log: in the container: chmod -R 777 storage/

!! IF you have issue with endpoints (404) please log into the container : "docker exec -it  php-apache bash"
and execute:

a2enmod rewrite
service apache2 restart

!!