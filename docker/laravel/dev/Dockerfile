# Installing PHP build
FROM php:8.2-fpm

# For Laravel 12.x; Minimums. Note that ctype, curl, filter, hash, openssl, pcre, session, PDO already precompiled to core PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip sqlite3 libsqlite3-dev

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/

# Ensure vendor/bin is in PATH
ENV PATH="$PATH:/var/www/vendor/bin"

# Default command: just sleep forever (so container stays running)
CMD ["tail", "-f", "/dev/null"]
