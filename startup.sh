#!/bin/sh

echo "$(date '+%Y-%m-%d %H:%M:%S') Script started" >> /home/logs.txt

# File upload fix
sed -i 's|abort_unless|//&|g' /home/site/wwwroot/vendor/livewire/livewire/src/Controllers/FileUploadHandler.php
#sed -i '/tmpfile/a $tmpfname = tempnam(sys_get_temp_dir(), "");\n$tmpFile = fopen($tmpfname, "w");' /home/site/wwwroot/vendor/livewire/livewire/src/TemporaryUploadedFile.php
#sed -i 's/$tmpFile/\/\/&/' /home/site/wwwroot/vendor/livewire/livewire/src/TemporaryUploadedFile.php
mkdir /home/site/wwwroot/storage/app/public/livewire-tmp
chmod -R 755 /home/site/wwwroot/storage
echo "$(date '+%Y-%m-%d %H:%M:%S') File upload fix applied" >> /home/logs.txt

echo "$(date '+%Y-%m-%d %H:%M:%S') Installing Supervisor" >> /home/logs.txt
apt-get install supervisor -y && service supervisor start

# Laravel config
cd /home/site/wwwroot || exit 1
cp .env.azure .env
php artisan optimize:clear
cp laravel-queue.conf /etc/supervisor/conf.d/
php artisan key:generate --force
php artisan storage:link
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
service supervisor restart
service supervisor start
echo "$(date '+%Y-%m-%d %H:%M:%S') Laravel deploy task completed" >> /home/logs.txt
# Nginx config
cp /home/site/wwwroot/nginx/default /etc/nginx/sites-enabled/ && service nginx restart
echo "$(date '+%Y-%m-%d %H:%M:%S') NGINX configured" >> /home/logs.txt


echo "$(date '+%Y-%m-%d %H:%M:%S') Startup script ran successfully." >> /home/logs.txt
