<?php

namespace App\UI\Frontend\Controller;

// use Obullo\View\ViewController;
use App\UI\UIControllerInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Header Controller.
 */
class Header implements UIControllerInterface
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
        // $controller = new ViewController($container);
        // $navbar     = $controller->get('HeaderController');

        $this->view->header = "Example header";
    }

}