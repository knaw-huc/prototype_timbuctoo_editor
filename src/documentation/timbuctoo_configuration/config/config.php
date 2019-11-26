<?php

define('APPNAME', "Timbuctoo editor");
define('BASE_URL', 'http://localhost:8888/timpars/'); // wordt gebruikt in de html links
define('TWEAK_PATH', '/var/www/html/timpars/tweaks/'); // hier de mapping in de docker container
define('TIMBUCTOO_SERVER', 'http://timbuctoo_timbuctoo_1/v5/graphql?query='); // is de containername binnen het door docker-compose opgezette netwerk van containers

//define('TIMBUCTOO_SERVER', 'http://timbuctoo_timbuctoo_1/static/graphiql');
//define('TIMBUCTOO_SERVER', 'http://timbuctoo_timbuctoo_1/v5/graphql?query=&hsid=c812434d-ca3c-4c77-abc4-87f2c95a9d4f');
//define('APPNAME', "Timbuctoo editor");
//define('BASE_URL', 'http://www.huc.localhost/timpars/');
//define('TWEAK_PATH', '/Library/WebServer/Documents/timpars/tweaks/');