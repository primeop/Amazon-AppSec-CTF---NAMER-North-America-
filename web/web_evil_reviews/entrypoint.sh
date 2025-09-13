#!/bin/bash
# Secure entrypoint
chmod 600 /entrypoint.sh

# Initialize & Start MariaDB
mkdir -p /run/mysqld
chown -R mysql:mysql /run/mysqld
mysqld --user=mysql --console --skip-name-resolve --skip-networking=0 &

# Wait for mysql to start
while ! mysqladmin ping -h'localhost' --silent; do echo "mysqld not up yet" && sleep .2; done

mysql -u root << EOF
CREATE DATABASE ecore_db;

CREATE USER 'ecore_user'@'%' identified by 'ecore_pass';

GRANT ALL PRIVILEGES ON ecore_db.* TO 'ecore_user'@'%';
FLUSH PRIVILEGES;
EOF

# Seed Database
/usr/local/lsws/lsphp83/bin/php /www/bin/seed-db.php

# Start OpenLiteSpeed
echo "Starting OpenLiteSpeed..."
/usr/local/lsws/bin/lswsctrl start

# Start Supervisor
echo "Starting supervisor..."
/usr/bin/supervisord -c /etc/supervisord.conf