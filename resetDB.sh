# Reset DataBase
cp .dbup/properties.ini .dbup/properties.ini.tmp
rm .dbup/applied/V*
php dbup.phar init
rm .dbup/properties.ini
cp .dbup/properties.ini.tmp .dbup/properties.ini
rm .dbup/properties.ini.tmp
rm sql/V1__sample_select.sql
php dbup.phar up

