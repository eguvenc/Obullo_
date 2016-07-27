<?php
/*
|--------------------------------------------------------------------------
| Routes 
|--------------------------------------------------------------------------
| Typically there is a one-to-one relationship between a URL string and its 
| corresponding ( controller / folder / class ).
|
*/
$router->rewrite(['GET', 'POST'], '/backend(.*)', '/$1');  // Normalize all requests

$router->map('GET', '/', 'home/index');
