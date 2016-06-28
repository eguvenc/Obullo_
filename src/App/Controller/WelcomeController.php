<?php

namespace App\Controller;

use Obullo\Controller;

class WelcomeController extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        // var_dump($this->config->cookie->path);

        // $this->container->addServiceProvider('App\ServiceProvider\Mongo');
        // $this->container->get('mongo')->shared(['connection' => 'default']);

        // echo $this->layer->get('View/Controller', 'header');
        // echo $this->layer->get('Controller', 'examples/layers/dummy/index/1/2/3');
        // echo $this->layer->get('View/Controller', 'header');

        // echo $this->layer->get('Controller', 'examples/layers/dummy/index/1/2/3');
        // echo $this->layer->get('Controller', 'welcomes/dummy/index/4/5/6');
        // echo $this->layer->get('Controller', 'examples/layers/dummy/index/7/8/9');

        $this->view->model('base')->render('welcome.phtml');
    }
}
