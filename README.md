Installation
============

1. Create MySQL database for the application and execute the SQL in application/configs/schema.sql.
2. Copy application/configs/application.ini.default to application/configs/application.ini and update the database credentials in the ini file.
3. Add a virtual host to your webserver. An example for Apache is given in /vhost.example.


VirtualHost Example (Apache)
----------------------------

    <VirtualHost *:80>
        ServerName sageweb

        DocumentRoot /home/username/projects/sageweb/public/
        <Directory />
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>

        LogLevel error
        CustomLog "/home/username/projects/sageweb/data/logs/access.log" combined
        ErrorLog "/home/username/projects/sageweb/data/logs/error.log"
    </VirtualHost>

