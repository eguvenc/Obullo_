<?php
/*
|--------------------------------------------------------------------------
| Routes 
|--------------------------------------------------------------------------
| Typically there is a one-to-one relationship between a URL string and its 
| corresponding ( controller / folder / class ).
|
*/

$router->rewrite('GET', '(?:en|de|es|tr)|/backend(.*)', '$1');  // example.com/en/  (or) // example.com/en


// $router->rewrite(['GET', 'POST'], '/backend(.*)', '/$1');  // Normalize all requests


$router->map('GET', '/', 'home/index');

// $router->group(
//     'users/',
//     function ($request, $response) use ($router) {

//         $router->group(
//             'test/',
//             function () use ($router) {

// 				echo 'ok';
//             }
//         );

//     }
// );