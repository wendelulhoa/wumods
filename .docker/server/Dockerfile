FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive

# Atualizando a lista de pacotes do repositório
RUN apt update -y && \
    # Atualizando os pacotes do sistema operacional
    apt upgrade -y && \
    # Instalando:
      # nano
    apt install -y nano \
      # Vim
      vim \
      # Alien
      alien \
      # Libaio
      libaio1 \
      #Wget
      wget \
      # Telnet
      telnet \
      # OpenVpn
      openvpn \
      # Brotli
      brotli \
      # Apache 2
      apache2 \
      # Curl
      curl \
      # Ping
      iputils-ping\
      # Lib Imagick\
      libmagickwand-dev \
      # Image Magick
      imagemagick \
      # Git
      git \
      # Instalando pré-requisitos para adição de novos repositórios
      software-properties-common && \
      # Adicionando repositório php
      add-apt-repository -y ppa:ondrej/php && \
      # Atualizando listagem de pacotes do repositório
      apt update -y

# Instalando PHP, PEAR e PECL
RUN apt install -y php8.3 php8.3-common php8.3-dev libapache2-mod-php8.3 php8.3-sqlite3 \
        php8.3-pgsql php8.3-mysql php8.3-sybase php8.3-redis php8.3-mongodb php8.3-gd \
        php8.3-mbstring php8.3-curl php8.3-soap php8.3-zip php8.3-fpm php8.3-bcmath php8.3-xml \
        php8.3-intl php8.3-ldap php8.3-xmlrpc php8.3-mcrypt php8.3-odbc php8.3-pdo-dblib php8.3-dev php-pear php-php-gettext php-xml

# Instalando pacotes de complementos e compilação
RUN apt install -y libapache2-mod-log-sql-mysql unzip gcc g++ libpq-dev \
    libc-dev musl-dev unixodbc-dev make autoconf pkg-config

# Setando php 8.3
RUN update-alternatives --set php /usr/bin/php8.3

# Instalando o Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

# Instalando npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt install -y nodejs

# Instalando imagick
RUN git clone https://github.com/Imagick/imagick.git
RUN cd imagick && \
    phpize && \
    ./configure && \
    make && \
    make install


# Configuração do apache
# Habilitando mods do Apache2
RUN a2enmod rewrite && \
    # Extensão apache
    phpenmod mbstring && \
    # Permissão
    chmod -R 775 /var/www/html/

# Config Variaveis apache
ADD ./config/php-apache.ini /etc/php/8.3/apache2/php.ini
# Config Variaveis php cli
ADD ./config/php.ini /etc/php/8.3/cli/php.ini

# Variáveis apache
ENV LANG en_US.utf8
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Virtual Host
ADD ./apache/000-default.conf /etc/apache2/sites-available/docker.wumods.loc.conf
RUN a2ensite docker.wumods.loc.conf
RUN a2dissite 000-default.conf

RUN echo "ServerName dev-wumods" >> /etc/apache2/apache2.conf

# Habilitando autocomplete no bash terminal
RUN apt install -y bash-completion
RUN cat >> ~/.bashrc <<EOF
if ! shopt -oq posix; then
  if [ -f /usr/share/bash-completion/bash_completion ]; then
    . /usr/share/bash-completion/bash_completion
  elif [ -f /etc/bash_completion ]; then
    . /etc/bash_completion
  fi
fi
EOF

WORKDIR /var/www/html/wumods

RUN service apache2 restart

## Cria usuário
ARG WWWUSER
ARG WWWGROUP
RUN useradd -G www-data,root -u $WWWGROUP -d /home/$WWWUSER $WWWUSER
RUN mkdir -p /home/$WWWUSER/.composer && chown -R $WWWUSER:root /home/$WWWUSER
RUN usermod -aG sudo $WWWUSER

## Copia o script de inicialização para dentro do container
COPY start-container /usr/local/bin/start-container

### Limpeza e otimização do container
RUN rm -rf /var/lib/apt/lists/* && apt autoremove -y && apt clean

RUN chmod +x /usr/local/bin/start-container

EXPOSE 80

ENTRYPOINT ["start-container"]
