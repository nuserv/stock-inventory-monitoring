FROM ubuntu:18.04
ENV TZ=Asia/Manila
ENV DEBIAN_FRONTEND=noninteractive
RUN 	ln -fs /usr/share/zoneinfo/Asia/Manila /etc/localtime
RUN 	apt-get update -y && \
	apt-get upgrade -y && \
	apt-get dist-upgrade -y
RUN apt-get install software-properties-common -y 
RUN	add-apt-repository ppa:ondrej/php
RUN apt-get update -y
RUN apt-get install php7.3 php7.3-fpm php7.3-curl php7.3-ldap php7.3-mysql php7.3-gd \
	php7.3-xml php7.3-mbstring php7.3-zip php7.3-bcmath composer curl wget nano -y
RUN apt-get purge apache2 apache* -y
WORKDIR /home/
COPY . .
RUN composer install
#RUN php artisan key:generate
RUN chmod 777 -R .
EXPOSE 8001
CMD php artisan serve --host 0.0.0.0 --port 8001
