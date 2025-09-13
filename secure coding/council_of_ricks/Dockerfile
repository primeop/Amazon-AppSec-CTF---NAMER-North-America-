# Use an official Debian base image
FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y nginx supervisor samba socat python3 python3-requests python3-bs4 libxml2-dev sqlite3 libsqlite3-dev && \
    docker-php-ext-install simplexml pdo_sqlite && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Configure supervisor
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/fpm.conf /usr/local/etc/php-fpm.conf
COPY config/nginx.conf /etc/nginx/nginx.conf

COPY challenge/app /www/app
COPY --chown=root:root challenge/flag.txt /

# Set up permissions
RUN chmod -R 777 /www && \
    chmod 400 /flag.txt

# Configure Samba
RUN echo "[app]" >> /etc/samba/smb.conf && \
    echo "path = /www" >> /etc/samba/smb.conf && \
    echo "browsable = yes" >> /etc/samba/smb.conf && \
    echo "writable = yes" >> /etc/samba/smb.conf && \
    echo "guest ok = yes" >> /etc/samba/smb.conf && \
    echo "read only = no" >> /etc/samba/smb.conf

# expose ports
EXPOSE 9000 445 1337

# Start supervisord
CMD ["/bin/sh", "-c", "service smbd start && /usr/bin/supervisord -c /etc/supervisord.conf"]
