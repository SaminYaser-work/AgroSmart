#!/bin/sh

# File upload fix
sed -i 's|abort_unless|//&|g' /home/site/wwwroot/vendor/livewire/livewire/src/Controllers/FileUploadHandler.php
sed -i '/tmpfile/a $tmpfname = tempnam(sys_get_temp_dir(), "");\n$tmpFile = fopen($tmpfname, "w");' /home/site/wwwroot/vendor/livewire/livewire/src/TemporaryUploadedFile.php
sed -i 's/$tmpFile/\/\/&/' /home/site/wwwroot/vendor/livewire/livewire/src/TemporaryUploadedFile.php
mkdir /home/site/wwwroot/storage/app/public/livewire-tmp
chmod -R 755 /home/site/wwwroot/storage

# Nginx config
cp /home/site/wwwroot/nginx/default /etc/nginx/sites-enabled/ && service nginx restart

# Laravel config
cd /home/site/wwwroot
cp .env.azure .env
php artisan key:generate --force
php artisan storage:link
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
