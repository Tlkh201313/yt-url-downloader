FROM php:7.4-fpm-alpine

# Install dependencies + latest yt-dlp
RUN apk update && apk add \
    curl \
    python3 \
    py3-pip \
    ffmpeg \
    && pip install --upgrade pip \
    && pip install --upgrade yt-dlp \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY . /var/www/html/

EXPOSE 9000

CMD ["php-fpm"]
