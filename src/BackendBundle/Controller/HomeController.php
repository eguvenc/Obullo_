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
        return $this->view->render('home.phtml');
    }
}
