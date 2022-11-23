
# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2020 Luiz Paulo "Bills"
# Version: 0.8
FROM sdobreff/gitpod

USER root

COPY gitpod-conf/default /etc/nginx/sites-available/default
COPY gitpod-conf/mysql_wordpress.sql $HOME/mysql_wordpress.sql
# COPY bashrc $HOME/.bashrc

RUN service mysql start && \
	mysql -u root < $HOME/mysql_wordpress.sql && \
	cd /var/www/html && \
	wp --allow-root core download && \
	wp --allow-root config create --dbname=wordpress --dbuser=wordpress --dbpass=password --dbhost=127.0.0.1 && \
	wp --allow-root core install --url=some --title=test --admin_user=admin --admin_email=test@test.com --admin_password=password && \
	chown -R www-data:www-data /var/www/html && \
	wp --allow-root config set WP_HOME "https://".\$_SERVER['HTTP_HOST'] --raw && \
	wp --allow-root config set WP_SITEURL "https://".\$_SERVER['HTTP_HOST'] --raw && \
	sed -i 's/https\:\/\/\./\"https:\/\/\"\./g' wp-config.php && \
	sed -i 's/HTTP_HOST/\"HTTP_HOST\"/g' wp-config.php && \
	sed -i '2s/^/$_SERVER["HTTPS"]="on";\n/' wp-config.php && \
	wp --allow-root plugin delete hello && \
	wp --allow-root plugin delete akismet