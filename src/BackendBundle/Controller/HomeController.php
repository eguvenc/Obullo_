<?php

namespace BackendBundle\Controller;

use Obullo\Mvc\Controller;
use Zend\Diactoros\Response\HtmlResponse;

class HomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
		return new HtmlResponse($this->render('home.phtml'));
    }
}
