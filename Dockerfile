# Use PHP 8.4 to match your local environment
FROM php:8.4-apache

# Install system dependencies (added libpq-dev for Postgres)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (added pdo_pgsql for Postgres)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Force check that the extension is enabled
RUN docker-php-ext-enable pdo_pgsql || true
RUN php -m | grep pdo_pgsql

# INCREASE PHP UPLOAD LIMITS (Crucial for Livewire/Render)
RUN echo "upload_max_filesize = 25M\npost_max_size = 25M" > /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package.json and install Node dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy existing application directory contents
COPY . .

# Run composer scripts now that artisan file is present
RUN composer dump-autoload --optimize
RUN php artisan package:discover --ansi

# Build frontend assets (Vite/Tailwind)
RUN npm run build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache DocumentRoot to point to Laravel's public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Use our custom start script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
