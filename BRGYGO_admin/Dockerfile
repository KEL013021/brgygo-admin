# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html/

# Copy all project files to the Apache root
COPY . .

# Set proper ownership and permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html

# Install required PHP extensions
RUN docker-php-ext-install mysqli

# Optional: Enable error reporting during development
# RUN echo "display_errors = On\nerror_reporting = E_ALL" > /usr/local/etc/php/conf.d/dev.ini

# Expose port 80 to allow traffic in
EXPOSE 80
