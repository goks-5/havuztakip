php artisan down

df -lh / 

echo "log dosyaları siliniyor...."

find /var/log/ -type f >logdosyalari.txt
while read line
do
    NAME=echo "$line" | cut -d'.' -f1
    EXTENSION=echo "$line" | cut -d'.' -f2
    rm -rf $NAME.gz
    :> "$line";
done <logdosyalari.txt
rm -rf logdosyalari.txt
rm -rf /var/log/-2
rm -rf /var/log/.2

echo "log dosyaları silindi"

df -lh / 

git pull

composer install --no-interaction --no-dev --prefer-dist

php artisan migrate --force

php artisan cache:clear

php artisan route:clear

php artisan config:clear

php artisan view:clear

php artisan up

php artisan mysqlVarible:set  