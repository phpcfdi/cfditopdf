# ------------------------------------
# Nuild stage
# ------------------------------------
FROM composer/composer:2 as build
WORKDIR /app

# Install dependencies
COPY composer.json ./
RUN composer install --no-dev --no-autoloader --no-scripts --ignore-platform-reqs
COPY . .
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs


# ------------------------------------
# Runtime stage
# ------------------------------------
FROM php:7-fpm-alpine as runtime

# System dependencies
RUN apk add --update libxslt-dev
RUN docker-php-ext-install xsl soap

# Copy source code
WORKDIR /app
COPY --from=build /app .

ENTRYPOINT ["bin/cfditopdf"]
CMD ["--help"]
