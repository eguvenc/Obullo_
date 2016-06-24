<?php

namespace App\UI\Frontend\Controller;

// use Obullo\View\ViewController;
use App\UI\UIControllerInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Footer Controller.
 */
class Footer implements UIControllerInterface
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
        // $navbar     = $controller->get('FooterController');

        $this->view->footer = "Example footer";
    }

}