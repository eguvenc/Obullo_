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
| DATA              - The full server path to the "data" folder
| TRANSLATIONS      - The full server path to the user "resources/translations" folder
| ASSETS            - The full server path to the "assets" folder
| TEMPLATES         - The full server path to the user "templates" folder
| TASK_FILE         - The file name for $php task operations.
*/
define('ROOT', dirname(__DIR__).'/');
define('APP',  ROOT .'app/');
define('CONFIG', APP. 'config/');
define('FOLDERS', APP .'folders/');
define('RESOURCES',  APP .'resources/');
define('DATA',  RESOURCES .'data/');
define('ASSETS',  ROOT .'public/assets/');
define('TRANSLATIONS',  RESOURCES .'translations/');
define('TEMPLATES',  RESOURCES . 'templates/');
define('TASKS', APP .'folders/tasks/');
define('TASK_FILE', 'task');