FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y software-properties-common
RUN apt-get install -y git

# PHP SETTING
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update && apt-get install -y php8.2
RUN apt-get install -y php8.2-mbstring php8.2-mysql php8.2-pdo php8.2-curl php8.2-xml php8.2-fpm
RUN apt-get install -y composer
RUN apt-get install -y php-pear
RUN apt-get install -y php-dev

# FFMPEG Setting
RUN apt-get update
RUN apt-get -y install ffmpeg

# git clone repository
WORKDIR /app
RUN git clone https://github.com/hyeokjonghan/cctv-api.git

WORKDIR /app/cctv-api
RUN composer install

# RUN composer install

EXPOSE 8000

CMD ["tail", "-f", "/dev/null"]
# CMD [ "php", "artisan", "serve" ]

# nginx는 EC2 내부에서 처리

# docker build --tag gigaeyes:1.0 .
# docker run -d --name test -p 8000:8000 -v C:/Users/jjong/tenis-cctv/.env:/app/cctv-api/.env gigaeyes:1.0