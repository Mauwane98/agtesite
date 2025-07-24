# Use the official PHP-Apache image
FROM php:8.1-apache

# Copy all files to the Apache web directory
COPY . /var/www/html/

# Enable Apache mod_rewrite (optional, useful for pretty URLs)
RUN a2enmod rewrite

# Expose the default Apache port
EXPOSE 80
