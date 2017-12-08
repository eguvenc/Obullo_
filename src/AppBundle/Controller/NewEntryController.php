<?php

namespace AppBundle\Controller;

use Obullo\Mvc\Controller;
use Zend\Diactoros\Response\HtmlResponse;

class NewEntryController extends Controller
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

        return new HtmlResponse("Hello");
    }
}
