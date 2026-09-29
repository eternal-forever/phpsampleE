FROM php:8.2-apache
RUN apt-get update && apt-get install -y libonig-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
# アップロードサイズ調整 (任意)
RUN echo "upload_max_filesize=5M\npost_max_size=10M" > /usr/local/etc/php/conf.d/uploads.ini
