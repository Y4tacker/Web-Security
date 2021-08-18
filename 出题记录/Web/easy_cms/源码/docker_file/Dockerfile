FROM php:7.4-fpm-alpine


COPY _files /tmp/
COPY source /var/www/html

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    && apk add --update --no-cache nginx \
	&& apk add --no-cache \
      libzip-dev \
      zip \
    && docker-php-ext-install zip \
    && mkdir /run/nginx \
    # configure file
    && mv /tmp/flag.sh /flag.sh \
    && mv /tmp/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint \
    && mv /tmp/nginx.conf /etc/nginx/nginx.conf \
    && chown -R www-data:www-data /var/www/html \
    && chmod +x /usr/local/bin/docker-php-entrypoint \
    # clear
    && rm -rf /tmp/*
    # && rm -rf /etc/apk


WORKDIR /var/www/html

EXPOSE 80

VOLUME ["/var/log/nginx"]

CMD ["/bin/bash", "-c", "docker-php-entrypoint"]