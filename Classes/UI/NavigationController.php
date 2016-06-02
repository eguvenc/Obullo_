<?php

namespace UI;

use Interop\Container\ContainerInterface as Container;

/**
 * Navigation Controller.
 */
class NavigationController implements UIControllerInterface
{
    protected $view;

    /**
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct(Container $container)
    {
        $this->view = $container->get('view');

        // $this->layer = new LayerRequest($container);
        // $this->layer->setFolder('views/controllers');
    }

    /**
     * Create component
     * 
     * @return void
     */
    public function create()
    {
        // $header = $this->layer->get('navbar');

        $this->view->assign('navbar', "Example navbar");
    }

}