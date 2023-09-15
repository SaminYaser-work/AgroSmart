#!/bin/sh

cp /home/site/wwwroot/nginx/default /etc/nginx/sites-enabled/ && service nginx restart
cd /home/site/wwwroot && cp .env.azure .env && php artisan key:generate && php artisan storage:link && php artisan config:cache && php artisan event:cache && php artisan route:cache && php artisan view:cache

sed -i 's|abort_unless(request()->hasValidSignature(), 401);|//&|g' /home/site/wwwroot/vendor/livewire/livewire/src/Controllers/FileUploadHandler.php

sed -i '/$tmpfile = tmpfile();/a $tmpfname = tempnam(sys_get_temp_dir(), '');\n$tmpFile = fopen($tmpfname, '\''w'\'');' -e 's/$tmpfile = tmpfile();/\/\/&/' /home/site/wwwroot/vendor/livewire/livewire/src/TemporaryUploadedFile.php

mkdir /home/site/wwwroot/storage/app/public/livewire-tmp

chmod -R 755 /home/site/wwwroot/storage
