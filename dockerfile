FROM php:7.4-apache

# Затаскиваем PHP-файл внутрь
COPY index.php /var/www/html/

# Устанавливаем расширения для работы с MySQL
RUN docker-php-ext-install mysqli
