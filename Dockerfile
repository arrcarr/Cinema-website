# Use the official PHP image with Apache pre-installed
FROM php:8.2-apache

# (Optional) If your app uses MySQL, uncomment the lines below to install PHP MySQL extensions
# RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

# Copy your source code into the default Apache public directory
COPY . /var/www/html/

# Expose port 80 to the outside world
EXPOSE 80