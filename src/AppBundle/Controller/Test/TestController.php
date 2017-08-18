<?php

namespace AppBundle\Controller\Test;

use Obullo\Mvc\Controller;
use Zend\Diactoros\Response\HtmlResponse;

class TestController extends Controller
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

        return new HtmlResponse("Hello Test Folder");
    }

    public function helloAction()
    {
        return new HtmlResponse("Hello Test Folder Hello Action");
    }

}
