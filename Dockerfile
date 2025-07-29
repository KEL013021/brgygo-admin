# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Enable mod_rewrite (needed for some routing or clean URLs)
RUN a2enmod rewrite

# Copy all your project files into the Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Give necessary permissions (optional but helpful)
RUN chown -R www-data:www-data /var/www/html

# Install PHP extensions if needed (e.g., mysqli, gd, etc.)
RUN docker-php-ext-install mysqli

# Expose port 80
EXPOSE 80
