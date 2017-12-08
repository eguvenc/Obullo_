<?php

namespace AppBundle\Controller;

use Obullo\Mvc\Controller;

class WelcomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction($request, $response)
    {
        // print_r($request->getArgs());

        

        // $this->console->log("test");

        return $response->getBody()->write($this->render('welcome.phtml'));
    }
}
