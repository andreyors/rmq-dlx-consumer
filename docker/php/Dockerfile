FROM php:7.2-cli-alpine

ENV GITHUB_OAUTH_TOKEN e9a40e4d0d5f667fac2f7a5b94cbe78ef9ff0446

RUN set -xe \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer config -g github-oauth.github.com $GITHUB_OAUTH_TOKEN

WORKDIR /www

CMD ["php", "-a"]
