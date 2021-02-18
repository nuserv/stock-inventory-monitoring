FROM ubuntu:18.04
ENV TZ=Asia/Manila
ENV DEBIAN_FRONTEND=noninteractive
RUN 	ln -fs /usr/share/zoneinfo/Asia/Manila /etc/localtime
RUN 	apt-get update -y && \
	apt-get upgrade -y && \
	apt-get dist-upgrade -y
RUN apt-get install software-properties-common -y 
RUN	add-apt-repository ppa:ondrej/php -y
RUN apt-get update -y
RUN apt-get install php7.4 php7.4-fpm php7.4-curl php7.4-ldap php7.4-mysql php7.4-gd \
	php7.4-xml php7.4-mbstring php7.4-zip php7.4-bcmath composer curl wget nano -y
RUN apt-get purge apache2 apache* -y
RUN apt-get remove --purge php8* -y
WORKDIR /home/
COPY . .
RUN composer install
#RUN php artisan key:generate
RUN chmod 777 -R .
EXPOSE 8001
CMD php artisan serve --host 0.0.0.0 --port 8001
