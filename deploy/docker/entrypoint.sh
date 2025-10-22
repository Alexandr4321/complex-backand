#!/bin/sh

mkdir -p "/var/www/storage/app/"
mkdir -p "/var/www/storage/app/files"
mkdir -p "/var/www/storage/app/public"
mkdir -p "/var/www/storage/framework/"
mkdir -p "/var/www/storage/framework/cache"
mkdir -p "/var/www/storage/framework/cache/data"
mkdir -p "/var/www/storage/framework/sessions"
mkdir -p "/var/www/storage/framework/testing"
mkdir -p "/var/www/storage/framework/views"
mkdir -p "/var/www/storage/logs/"
mkdir -p "/var/www/storage/logs/nginx"
mkdir -p "/var/www/storage/logs/queue"
mkdir -p "/var/www/storage/logs/scheduler"
chmod -R 777 "/var/www/storage"

exec "$@"
