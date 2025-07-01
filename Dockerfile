FROM php:8.1-apache

# Copy semua file ke direktori web
COPY . /var/www/html/

# Ubah port Apache ke 8080 (biar cocok dengan Render)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf \
 && sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf

# Expose port 8080
EXPOSE 8080

CMD ["apache2-foreground"]
