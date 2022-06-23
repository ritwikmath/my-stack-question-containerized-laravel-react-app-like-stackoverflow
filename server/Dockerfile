FROM php:8.1-fpm-buster

# Arguments
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# clear server cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a new user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install NodeJS 16
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs

# Copy project to Container
COPY ./ /var/www

# Set working directory
WORKDIR /var/www

# Create .env file
# RUN cp .env.example .env

# Install composer packages
RUN composer install

# Generate artisan key
RUN php artisan key:Generate

# Create the symbolic link
RUN php artisan storage:link

# Install NPM Packages and compile
RUN npm install
RUN npm run dev

RUN chown $user -R ./

# Set user
USER $user
