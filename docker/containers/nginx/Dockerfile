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

#--- nginx ---
RUN apt-get install nginx -y

#--- clean ---
RUN rm -rf /tmp/*
RUN apt-get clean

ENTRYPOINT /usr/sbin/nginx