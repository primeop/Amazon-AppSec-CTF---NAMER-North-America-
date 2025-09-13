FROM python:3.11.2-bullseye


# Install packages
RUN apt update \
    && apt -y install supervisor gcc wget curl mariadb-server \
    && rm -rf /var/lib/apt/lists/*


# Install OpenLiteSpeed
WORKDIR /tmp
RUN wget https://openlitespeed.org/packages/openlitespeed-1.7.16.tgz \
    && tar xzf openlitespeed-1.7.16.tgz \
    && cd openlitespeed \
    && ./install.sh \
    && rm -rf /tmp/openlitespeed* 


# Configure OpenLiteSpeed for reverse proxy
RUN mkdir -p /tmp/lshttpd/swap /usr/local/lsws/logs \
    && mkdir -p /usr/local/lsws/conf/vhosts/webserver \
    && chown -R nobody:nogroup /tmp/lshttpd /usr/local/lsws/logs
COPY conf/httpd_config.conf /usr/local/lsws/conf/httpd_config.conf
COPY conf/vhconf.conf /usr/local/lsws/conf/vhosts/webserver/vhconf.conf


# Upgrade PHP
RUN apt install -y lsphp83 lsphp83-common lsphp83-mysql


# Install Python requirements
COPY conf/requirements.txt /root/requirements.txt
RUN pip install -r /root/requirements.txt \
    && rm /root/requirements.txt


# Disable buffered Python & pycache
ENV PYTHONUNBUFFERED=TRUE
ENV PYTHONDONTWRITEBYTECODE=1


# Copy application files
COPY challenge/web /www
COPY challenge/pyapi /pyapi


# Install PHP dependencies
WORKDIR /www
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN PATH="/usr/local/lsws/lsphp83/bin:$PATH" composer install
RUN chown -R www-data:www-data /pyapi /www


# Setup flag
COPY flag.txt /root/flag
COPY conf/readflag.c /root/readflag.c

RUN gcc /root/readflag.c -o /readflag \
    && chown root:root /readflag \
    && chmod +s /readflag \
    && rm /root/readflag.c \
    && chmod 400 /root/flag


# Setup superivsord
COPY conf/supervisord.conf /etc/supervisord.conf


# Expose web server
EXPOSE 80


# Start supervisord
COPY --chown=root entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
