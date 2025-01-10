FROM python:3.10
RUN apt-get update

#--- tools ---
RUN apt-get install \
    curl \
    host \
    mc \
    nmap \
    wget \
    net-tools -y

RUN apt-get install python3-dev default-libmysqlclient-dev build-essential -y

COPY requirements.txt /venv/requirements.txt
RUN pip install --no-cache-dir -r /venv/requirements.txt

WORKDIR /code