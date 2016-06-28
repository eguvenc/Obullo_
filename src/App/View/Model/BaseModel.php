<?php

namespace App\View\Model;

use Obullo\View\ViewModelInterface;
use Interop\Container\ContainerInterface as Container;

class BaseModel implements ViewModelInterface
{
    protected $view;
    protected $layer;
    protected $header;
    protected $footer;

    /**
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct(Container $container)
    {
        $this->view  = $container->get('view');
        $this->layer = $container->get('layer');
    }

    /**
     * Set template variables
     * 
     * @return void
     */
    public function setTemplate()
    {
        $this->view->header = $this->layer->get('header', array(), null, 'View/Controller');
        $this->view->footer = $this->layer->get('footer', array(), null, 'View/Controller');
    }

}