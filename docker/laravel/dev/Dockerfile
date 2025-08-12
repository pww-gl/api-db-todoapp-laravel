# Installing PHP build
FROM php:8.3.6-cli

# For Laravel 12.x; Minimums. Note that ctype, curl, filter, hash, openssl, pcre, session, PDO already precompiled to core PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip sqlite3 libsqlite3-dev

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/

# Ensure vendor/bin is in PATH
ENV PATH="$PATH:/var/www/vendor/bin"

ARG UID=1000
ARG GID=1000
ARG UNAME=dan-wyn

RUN groupadd -g $GID $UNAME \
    && useradd -m -u $UID -g $GID $UNAME

USER $UNAME

# Default command: just sleep forever (so container stays running)
CMD ["tail", "-f", "/dev/null"]