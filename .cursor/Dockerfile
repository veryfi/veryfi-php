FROM php:8.5.12-cli-bookworm

ENV DEBIAN_FRONTEND=noninteractive

# Official PHP 8.5.12 CLI already includes the extensions required by
# composer.json (curl, json, mbstring, fileinfo) plus xml/dom for PHPUnit.
# xdebug is added for coverage.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        unzip \
        $PHPIZE_DEPS \
    && pecl install xdebug-3.5.3 \
    && docker-php-ext-enable xdebug \
    && php -r 'exit(PHP_VERSION === "8.5.12" ? 0 : 1);' \
    && php -m | grep -q '^curl$' \
    && php -m | grep -q '^mbstring$' \
    && php -m | grep -q '^fileinfo$' \
    && php -m | grep -q '^dom$' \
    && php -m | grep -q '^Xdebug$' \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

RUN useradd --create-home --shell /bin/bash --uid 1000 ubuntu

ENTRYPOINT []
CMD ["bash"]

USER ubuntu
