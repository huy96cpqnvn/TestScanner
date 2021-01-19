#!/bin/sh

/etc/init.d/php7.3-fpm start
crontab /etc/cron.d/schedule-cron
/etc/init.d/cron start

ln -s /var/www/html/storage/app/public /var/www/html/public/assets

cd /var/www/html && php artisan queue:listen --tries=3 >/dev/null 2>&1 &

cd /var/www/html && php artisan queue:work database --queue=schedules --daemon &
cd /var/www/html && php artisan queue:work database --queue=imports --daemon &
cd /var/www/html && php artisan queue:work database --queue=notifications --daemon &

#cd /var/www/html && composer dump-autoload

# cd /var/www/html && php artisan migrate:refresh --seed

cd /var/www/html && php artisan migrate

#cd /var/www/html && php artisan db:seed --class=MailTemplateSeeder
#cd /var/www/html && php artisan db:seed --class=MailTemplateLocaleSeeder
#cd /var/www/html && php artisan db:seed --class=RoleMenuSeeder
cd /var/www/html && php artisan db:seed --class=PermissionSeeder
#cd /var/www/html && php artisan db:seed --class=GroupSeeder
#cd /var/www/html && php artisan db:seed --class=PaymentGroupSeeder
#cd /var/www/html && php artisan db:seed --class=PaymentMethodSeeder
#cd /var/www/html && php artisan db:seed --class=RoleSeeder
cd /var/www/html && php artisan db:seed --class=RolePermissionSeeder
#cd /var/www/html && php artisan db:seed --class=PermissionGroupSeeder
#cd /var/www/html && php artisan db:seed --class=RoleMenuSeeder
#cd /var/www/html && php artisan db:seed --class=AirportSeeder

