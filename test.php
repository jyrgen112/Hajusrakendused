<?php
echo shell_exec('cd /data01/virt124153/domeenid/www.tak23vander.itmajakas.ee/htdocs && php artisan tinker --execute="echo env(\'OPENWEATHER_API_KEY\');"');