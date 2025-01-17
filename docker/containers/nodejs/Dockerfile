FROM node:22-slim
RUN apt-get update

#--- tools ---
RUN apt-get install \
    curl \
    host \
    mc \
    nmap \
    wget \
    htop \
    net-tools -y

WORKDIR /code