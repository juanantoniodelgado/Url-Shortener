FROM php:fpm-alpine
#COPY wait-for-it.sh /usr/bin/wait-for-it
#RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add coreutils git;

# PDO CONFIGURATION
RUN docker-php-ext-install pdo_mysql;

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; bin/console doctrine:migrations:migrate ;  php-fpm
EXPOSE 9000