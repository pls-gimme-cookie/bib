# Используем образ PHP с Apache
FROM php:7.4-apache

# Устанавливаем необходимые расширения для работы с MySQL
RUN docker-php-ext-install mysqli

# Указываем переменные среды для конфигурации базы данных
ENV MYSQL_HOST=postgres.railway.internal \
    MYSQL_USER=postgres \
    MYSQL_PASSWORD=ezYSBIUSpCcnUDLAMJEYQuHRpiEenPbE \
    MYSQL_DATABASE=railway

# Копируем PHP-файл внутрь контейнера
COPY index.php /var/www/html/

# Открываем порты
EXPOSE 80
