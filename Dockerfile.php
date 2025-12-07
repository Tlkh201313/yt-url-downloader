# Dockerfile.php
# Use a standard PHP 7.4 FPM image as the base
FROM php:7.4-fpm-alpine

# Install necessary dependencies (git, yt-dlp via pip) for the backend logic
# This installs the Alpine Linux package manager tools (apk) and Python 3's pip
RUN apk update && apk add \
    git \
    unzip \
    zip \
    curl \
    py3-pip \
    && pip install yt-dlp \
    && rm -rf /var/cache/apk/*

# Set the working directory inside the container
WORKDIR /var/www/html

# We use the COPY . /var/www/html command because the VOLUME in docker-compose.yml (./:/var/www/html)
# will handle injecting the source files at runtime, but COPY ensures the container builds correctly.
# NOTE: The previous error came from the wrong source path; COPY . /var/www/html is a safe, generic approach.
COPY . /var/www/html/

# Expose port 9000 for PHP-FPM to connect to the Nginx container
EXPOSE 9000

# The default command runs PHP-FPM
CMD ["php-fpm"]