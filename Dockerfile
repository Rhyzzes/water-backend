# Use official PHP image with Apache
FROM php:8.1-apache

# Enable Apache mod_rewrite (for frameworks like Laravel, etc.)
RUN a2enmod rewrite

# Copy the PHP app from back_end folder into Apache's web root
COPY back_end/ /var/www/html/

# Set correct permissions so Apache can serve the files
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
