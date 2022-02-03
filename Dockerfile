FROM wordpress:php7.4-apache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update --fix-missing && \
    apt-get install -y --no-install-recommends 

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy our wordpress files
COPY --chown=www-data:www-data wordpress/. /usr/src/wordpress/

# Misc deps 
RUN apt-get install -y openssh-server rsync mariadb-client

# Modify `sshd_config`
RUN sed -ri 's/#PermitRootLogin prohibit-password/PermitRootLogin prohibit-password/' /etc/ssh/sshd_config
RUN sed -ri 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
RUN sed -ri 's/www:\/usr\/sbin\/nologin/www:\/bin\/bash/' /etc/passwd