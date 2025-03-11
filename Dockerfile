FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli zip mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN composer install --no-dev --optimize-autoloader

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]