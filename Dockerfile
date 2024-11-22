# Primera etapa - Solo vendor
FROM php:7.4-fpm as vendor-stage
WORKDIR /temp
# Copia solo la carpeta vendor
COPY vendor/ ./vendor/
# Crear un archivo tar con las dependencias
RUN tar -czf vendor.tar.gz vendor/

# Segunda etapa - Imagen final ligera
FROM php:7.4-fpm

WORKDIR /var/www/html

# Copia solo el archivo tar de las dependencias
COPY --from=vendor-stage /temp/vendor.tar.gz /tmp/vendor.tar.gz

# Extrae las dependencias y limpia
RUN tar -xzf /tmp/vendor.tar.gz \
    && rm /tmp/vendor.tar.gz \
    && chown www-data:www-data vendor

EXPOSE 9000