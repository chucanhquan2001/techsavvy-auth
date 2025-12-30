FROM php:8.3-cli-alpine

WORKDIR /var/www

# 1. Sử dụng script tối ưu để cài extension (tự dọn dẹp rác build)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mbstring zip bcmath gd pcntl swoole opcache

# 2. Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. TỐI ƯU CACHE: Copy file cấu hình trước
COPY composer.json composer.lock ./

# Cài library mà không chạy script hay autoload (để cache layer này)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 4. Copy toàn bộ code vào
COPY . .

# 5. Lúc này mới chạy autoload và tối ưu hóa
RUN composer install --no-dev --optimize-autoloader

# 6. Cấu hình Runtime (OpCache & Timezone)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/UTC/Asia\/Ho_Chi_Minh/g' "$PHP_INI_DIR/php.ini"

# Cài đặt OpCache tối ưu cho Octane
RUN { \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# 7. Bảo mật: Chạy bằng user www-data thay vì root
RUN chown -R www-data:www-data /var/www
USER www-data

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]