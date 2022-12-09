if [ -e "/var/www/html/stock/storage/app/Master.csv" ]
then
mv /var/www/html/stock/storage/app/Master.csv /var/www/html/cdm/html/Master.csv
echo 1 > /var/www/html/cdm/html/update.txt
fi
