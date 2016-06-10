<?php

use Obullo\Controller;

class Welcome extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
        // echo $this->layer->get('views/controllers', 'header');
        // echo $this->layer->get('controllers', 'examples/layers/dummy/index/1/2/3');
        // echo $this->layer->get('views/controllers', 'header');

        // echo $this->layer->get('controllers', 'examples/layers/dummy/index/1/2/3');
        // echo $this->layer->get('controllers', 'welcomes/dummy/index/4/5/6');
        // echo $this->layer->get('controllers', 'examples/layers/dummy/index/7/8/9');

        $this->view->render('views::welcome');
    }
}
