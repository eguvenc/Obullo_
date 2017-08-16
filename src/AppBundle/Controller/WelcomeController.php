<?php

namespace AppBundle\Controller;

use Obullo\Mvc\View;
use Obullo\Mvc\Controller;
use Zend\Diactoros\Response\HtmlResponse;

class WelcomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction($request)
    {

        // print_r($request->getArgs());

        // $this->console->log("test");

        return new HtmlResponse($this->render('welcome.phtml'));
    }
}
