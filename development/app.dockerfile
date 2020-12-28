FROM php:7.2-fpm

COPY composer.lock composer.json /var/www/


COPY database /var/www/database


WORKDIR /var/www


RUN apt-get update && apt-get -y install git && apt-get -y install zip


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php -r "if (hash_file('SHA384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && php composer-setup.php && php -r "unlink('composer-setup.php');" && php composer.phar install --no-dev --no-scripts && rm composer.phar


COPY . /var/www


RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache


RUN php artisan cache:clear


RUN php artisan optimize


RUN  apt-get install -y libmcrypt-dev libmagickwand-dev --no-install-recommends && pecl install mcrypt-1.0.2 && docker-php-ext-install pdo_mysql && docker-php-ext-enable mcrypt


RUN mv .env.prod .env


RUN php artisan optimize