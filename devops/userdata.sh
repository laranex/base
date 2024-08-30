#!/bin/bash

# Update the system
sudo apt update -y

sudo apt install -y nginx git curl unzip zip redis

# Install PHP-8.1
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update -y
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-common php8.3-cli php8.3-xmlrpc php8.3-soap php8.3-intl php8.3-bcmath php8.3-ldap php8.3-imap php8.3-imagick php8.3-redis php8.3-imagick 

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

sudo apt remove -y apache2
sudo systemctl enable --now php8.3-fpm
sudo systemctl enable --now nginx
sudo systemctl enable --now redis

# Install MySQL
sudo apt install -y mysql-server

sudo systemctl enable --now mysql
sudo mysql_secure_installation

# Create a new user, database, and grant privileges
sudo mysql -u root <<EOF
CREATE DATABASE laranex_pacakge_base;
CREATE USER 'onenex'@'localhost' IDENTIFIED WITH mysql_native_password BY 'p@ssw0rd';
GRANT SELECT, REFERENCES ON *.* TO 'onenex'@'localhost';
GRANT ALL PRIVILEGES ON laranex_pacakge_base.* TO 'onenex'@'localhost';
FLUSH PRIVILEGES;
EOF

cd /var/www/html
sudo chown -R ubuntu:www-data /var/www/html
git clone https://github.com/laranex/base.git /var/www/html/base
git submodule update --init --recursive
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed


