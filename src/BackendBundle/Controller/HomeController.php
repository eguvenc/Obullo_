<?php

namespace BackendBundle\Controller;

use Obullo\Mvc\Controller;

class HomeController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->render('home.phtml');
    }
}
