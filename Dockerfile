FROM ubuntu:18.04
ENV TZ=Asia/Manila
ENV DEBIAN_FRONTEND=noninteractive
RUN 	ln -fs /usr/share/zoneinfo/Asia/Manila /etc/localtime
RUN 	apt-get update -y && \
	apt-get upgrade -y && \
	apt-get dist-upgrade -y && \
	apt-get install software-properties-common php7.2 php7.2-fpm php7.2-curl php7.2-ldap php7.2-mysql php7.2-gd \
	php7.2-xml php7.2-mbstring php7.2-zip php7.2-bcmath composer curl wget nano -y
RUN apt-get purge apache2 apache* -y
WORKDIR /home/
COPY . .
RUN composer install
#RUN php artisan key:generate
RUN chmod 777 -R .
EXPOSE 8001
CMD php artisan serve --host 0.0.0.0 --port 8001
