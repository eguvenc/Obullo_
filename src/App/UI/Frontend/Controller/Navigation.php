<?php

namespace App\UI\Frontend\Controller;

// use Obullo\View\ViewController;
use App\UI\UIControllerInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Navigation Controller.
 */
class Navigation implements UIControllerInterface
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
        // $navbar     = $controller->get('NavbarController');

        $this->view->navbar = "Example navbar";
    }

}