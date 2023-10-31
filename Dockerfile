# Use the specified image as the base
FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && \
    apt-get install -y git unzip libcurl4-openssl-dev && \
    docker-php-ext-install bcmath curl


# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory in container
WORKDIR /app

# Copy Composer files into the working directory
COPY composer.json ./

# Install Composer dependencies
RUN composer install

# Copy all files into the working directory
COPY . .

# Change permissions if necessary
RUN chown -R www-data:www-data /app

# Run the PHP script when the container starts
# CMD ["php", "script.php", "input.csv"]