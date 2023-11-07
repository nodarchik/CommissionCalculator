# Use the specified PHP image as the base
FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && \
    apt-get install -y git unzip libcurl4-openssl-dev && \
    docker-php-ext-install bcmath curl

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory in the container
WORKDIR /app

# Copy only the Composer files to take advantage of Docker's layer caching
COPY composer.json composer.lock ./

# Install Composer dependencies without scripts
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application files
COPY . .

# Finish Composer's autoload dump and optimize autoloader
RUN composer dump-autoload --optimize

# Update permissions to ensure the www-data user can access the files
RUN chown -R www-data:www-data /app

# Define a default command, if needed
CMD ["php", "-S", "0.0.0.0:9000"]
