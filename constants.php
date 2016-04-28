<?php
/*
|---------------------------------------------------------------
| ESSENTIAL CONSTANTS
|---------------------------------------------------------------
| ROOT              - The root path of your server
| APP               - The full server path to the "app" folder
| CONFIG            - The full server path to the "config" folder
| FOLDERS       	- The full "dynamic" server path to the "modules" folder
| RESOURCES         - The full server path to the user "resources" folder
| ASSETS            - The full server path to the "assets" folder
| TASKS       		- The file name for $php task operations.
*/
define('APP',  ROOT .'app/');
define('CONFIG', APP. 'config/');
define('FOLDERS', APP .'folders/');
define('RESOURCES',  APP .'resources/');
define('ASSETS',  ROOT .'public/assets/');
define('TASKS', APP .'tasks/');