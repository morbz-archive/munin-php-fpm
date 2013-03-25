Setup PHP-FPM
-------------

This plugin requires PHP CLI.

- ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-status : Provides graph about php-fpm processes status  
- ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-memory : Provides graph about Memory processes usage  
- ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-memoryPreview : Provides graph about Memory processes pools usage (RAM max, RAM min and RAM average)  
- ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-cpu : Provides graph about processes CPU usage  

More information
----------------

See https://github.com/sosedoff/munin-plugins/tree/master/php-fpm for installation informations and to see another php-fpm plugin.
