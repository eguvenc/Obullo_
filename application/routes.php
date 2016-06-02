<?php
/*
|--------------------------------------------------------------------------
| Routes 
|--------------------------------------------------------------------------
| Typically there is a one-to-one relationship between a URL string and its 
| corresponding ( folders / controller / method ).
|
*/
// $router->map('GET', '/users/(.*)',
//     function ($request, $response, $args) use($router) {

//         $response->getBody()->write('Users group');

//         // $this->add();

//         return $response;
//     }
// );

$router->rewrite('GET', '(?:en|de|es|tr)|/(.*)', '$1');  // example.com/en/  (or) // example.com/en

$router->map('GET', '/', 'welcome/index');
// $router->map('GET', '/|welcome', 'welcome/index');

// $router->map('GET', '/users/(\w+)/(\d+)', '/users/$1/$2');

// $router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response, $args) {
//     var_dump($args);
// });

$router->group(
    'users/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                // $router->map('GET', '/users/(\w+)/(\d+)', '/users/$1/$2');

                // throw new \Exception("cimcime");

                $router->map(
                    'GET',
                    '/users/(\w+)/(\d+).*',
                    function ($request, $response, $args) {
                        
                        // var_dump($args);
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest');

                // ->filter('contains', ['/users/test/45'])->add('Guest');

                //->filter('notContains', ['/users/teZ'])->add('Guest');;
                //
                // ->ifContains(['login'])
                // ->ifNotContains(['login', 'payment'])
                // ->ifRegExp(['welcome/path/index'])
                // ->ifNotRegExp(['welcome/path/index'])

            }
        );

    }
);