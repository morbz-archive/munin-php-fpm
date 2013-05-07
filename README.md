This is a revised version of [Devlopnet / munin-php-fpm](https://github.com/Devlopnet/munin-php-fpm) with the difference that processes are combined based on their pools. Also there is an average process age graph and a process count graph.

Setup PHP-FPM
-------------

This plugin requires PHP CLI.

### Install Plugin
`$ cd /usr/share/munin/plugins/`  
`$ [sudo] wget -O php-fpm https://github.com/MorbZ/munin-php-fpm/blob/master/php-fpm.php`  
`$ [sudo] chmod +x php-fpm`

### Setup Graphs
Average process memory per pool:  
`$ [sudo] ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-memory`

CPU per pool:  
`$ [sudo] ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-cpu`

Number of processes per pool:  
`$ [sudo] ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-count`

Average process age per pool:  
`$ [sudo] ln -s /usr/share/munin/plugins/php-fpm /etc/munin/plugins/php-fpm-time`

Don't forget to restart Munin after changing plugins:  
`$ [sudo] service munin-node restart`