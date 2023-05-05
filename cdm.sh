#sleep 1m
update-alternatives --set php /usr/bin/php7.4 && cd /var/www/html/stock && php artisan cdm:processor
if [ -e "/var/www/html/stock/storage/app/Master.csv" ]
then
mv /var/www/html/stock/storage/app/Master.csv /var/www/html/cdm/html/Master.csv
currenttime=$(date +%H:%M)
	if [[ "$currenttime" < "00:13" ]]; then
		echo "1" > /var/www/html/cdm/html/update.txt
	else
		echo "0" > /var/www/html/cdm/html/update.txt
	fi
cd ../cdm/html && bash /var/www/html/cdm/html/600a.sh && cd ../../stock
#cd /var/www/html/stock && php artisan cdm:processor
#mv /var/www/html/stock/storage/app/Master.csv /var/www/html/cdm/html/Master.csv
#echo "4" > /var/www/html/cdm/html/update.txt
#cd ../cdm/html && bash /var/www/html/cdm/html/600a.sh && cd ../../stock
fi
