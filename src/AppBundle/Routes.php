<?php
/*
|--------------------------------------------------------------------------
| Routes 
|--------------------------------------------------------------------------
| Typically there is a one-to-one relationship between a URL string and its 
| corresponding ( controller / folder / class ).
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

// $router->map('GET', '/newEntry', 'NewEntry/index');

$router->map('GET', '/', 'Welcome/index');
$router->map('GET', 'welcome', 'Welcome/index');
$router->map('GET', 'welcome/index/(\d+)', 'Welcome/index/$1');

$router->map('GET', 'test', 'Test/Test/index');

// $router->map('GET', '/users/(\w+)/(\d+)', '/Users/$1/$2');
// $router->map('GET', '/users/(\w+)/(\d+)', function ($request, $response, $args) {
//     var_dump($args);
// });

$router->group(
    'users/',
    function () use ($router) {

        $router->group(
            'test/',
            function () use ($router) {

                // $router->map('GET', 'users/test/(\w+)/(\d+)', 'Welcome/index/$1/$2');

                // throw new \Exception("cimcime");
                
                $router->map(
                    'GET',
                    'users/test/(\w+)/(\d+).*',
                    function ($request, $response, $args) {
                        
                        // var_dump($args);
                        
                        $response->getBody()->write("yES !");

                        return $response;
                    }
                )->add('Guest');


                //->filter('contains', ['users/test/45'])->add('Guest');

                //->filter('notContains', ['users/teZ'])->add('Guest');;
                //
                // ->ifContains(['login'])
                // ->ifNotContains(['login', 'payment'])
                // ->ifRegExp(['welcome/path/index'])
                // ->ifNotRegExp(['welcome/path/index'])
            }
        );
    }
);
