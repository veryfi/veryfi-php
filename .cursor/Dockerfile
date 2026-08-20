FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

# System packages + PHP 8.3 with the extensions required by composer.json
# (curl, json, mbstring, fileinfo) plus xml/dom and xdebug for PHPUnit coverage.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        unzip \
        php8.3-cli \
        php8.3-curl \
        php8.3-mbstring \
        php8.3-xml \
        php8.3-xdebug \
    && rm -rf /var/lib/apt/lists/*

USER ubuntu
