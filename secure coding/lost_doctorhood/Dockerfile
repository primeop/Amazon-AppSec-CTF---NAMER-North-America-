# Use the official image as a parent image
FROM debian:latest@sha256:1dc55ed6871771d4df68d393ed08d1ed9361c577cfeb903cd684a182e8a3e3ae

# Install necessary packages and Python libraries
RUN apt-get update && \
    apt-get install -y supervisor php-fpm nginx samba python3 python3-pip socat php-sqlite3 && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN python3 -m pip install semgrep --break-system-packages

# Configure PHP-FPM, Nginx, and Supervisord
COPY config/fpm.conf /etc/php/8.2/fpm/php-fpm.conf
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/nginx.conf /etc/nginx/nginx.conf

# Copy challenge and checker files
COPY challenge/application /www/application
#COPY --chown=root:root challenge/validate.py /tmp
#COPY --chown=root:root challenge/semgrep_rules.yaml /tmp
COPY --chown=root:root challenge/flag.txt /flag.txt
#COPY challenge/application/1Bl39ewB9b.so /tmp/malware.so

# Set up permissions
RUN chmod 400 /flag.txt
RUN chmod -R 777 /www
#RUN chmod 400 /tmp/validate.py && chmod 400 /tmp/semgrep_rules.yaml
RUN chown -R www-data:www-data /var/lib/nginx

# Disable Python caching
ENV PYTHONDONTWRITEBYTECODE=1

# Configure Samba
RUN echo "[app]" >> /etc/samba/smb.conf && \
    echo "path = /www/application" >> /etc/samba/smb.conf && \
    echo "browsable = yes" >> /etc/samba/smb.conf && \
    echo "writable = yes" >> /etc/samba/smb.conf && \
    echo "guest ok = yes" >> /etc/samba/smb.conf && \
    echo "read only = no" >> /etc/samba/smb.conf

# Expose necessary ports
EXPOSE 3000 445

# Start socat (as an additional service) and supervisord
CMD service smbd start && /usr/bin/supervisord -c /etc/supervisord.conf
