<?php

namespace UI;

use Interop\Container\ContainerInterface as Container;

/**
 * Footer Controller.
 */
class HeaderController implements UIControllerInterface
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
    }

    /**
     * Create component
     * 
     * @return void
     */
    public function create()
    {
        // $header = $this->layer->get('views/controllers', 'header');

        $this->view->assign('header', "Example footer");
    }

}