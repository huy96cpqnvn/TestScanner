FROM nginx

RUN apt-get update -y && apt-get upgrade -y

RUN apt-get install -y procps

###########################################################################
# Install php
###########################################################################

RUN apt install -y php7.3-fpm
RUN apt install -y php7.3-common php7.3-mysql php7.3-xml php7.3-xmlrpc php7.3-curl php7.3-gd php7.3-imagick php7.3-cli php7.3-imap php7.3-mbstring php7.3-opcache php7.3-zip php7.3-intl

###########################################################################
# Install node & npm & pm2
###########################################################################

RUN apt-get install -y nodejs
RUN apt-get install -y npm && npm install npm@latest -g
RUN npm install -g pm2

###########################################################################
# Install crontab
###########################################################################

RUN apt-get -y install cron


###########################################################################
# Install composer
###########################################################################

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '795f976fe0ebd8b75f26a6dd68f78fd3453ce79f32ecb33e7fd087d39bfeb978342fb73ac986cd4f54edd0dc902601dc') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

