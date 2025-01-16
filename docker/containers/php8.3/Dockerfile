FROM ubuntu:24.04
RUN apt-get update

#--- tools ---
RUN apt-get install \
    curl \
    host \
    mc \
    nmap \
    wget \
    net-tools -y

#--- php ---
RUN apt-get install software-properties-common -y
RUN LC_ALL=C.UTF-8 add-apt-repository -yu ppa:ondrej/php
RUN apt-get update
RUN apt-get install php-pear -y
RUN apt-get install php8.3 -y
RUN apt-get install php8.3-cli -y
RUN apt-get install php8.3-common -y
RUN apt-get install php8.3-xml -y
RUN apt-get install php8.3-curl -y
RUN apt-get install php8.3-fpm -y
RUN apt-get install php8.3-soap -y
RUN apt-get install php8.3-gd -y
RUN apt-get install php8.3-mysql -y
RUN apt-get install php8.3-mysqli -y
RUN apt-get install php8.3-readline -y
RUN apt-get install php8.3-mbstring -y
RUN apt-get install php8.3-dev -y
RUN apt-get install libzip-dev -y
RUN apt-get install zip -y
RUN update-alternatives --set php /usr/bin/php8.3

#--- composer ---
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

#--- clean ---
RUN rm -rf /tmp/*
RUN apt-get clean
