FROM php:8.4-cli-alpine

COPY . /opt/source
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# install git, ext-zip and ext-xsl and ext-gd
RUN apk add git libzip libxslt freetype libjpeg-turbo libpng \
    libzip-dev libxslt-dev freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev libxslt-dev && \
    docker-php-ext-install zip xsl gd && \
    apk del libzip-dev libxslt-dev freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev libxslt-dev

# build project
RUN cd /opt/source && \
    rm -r -f composer.lock vendor && \
    composer update --no-dev && \
    composer check-platform-reqs

ENTRYPOINT ["/usr/local/bin/php", "/opt/source/bin/cfditopdf"]
