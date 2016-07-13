<?php

namespace App\View\Template;

use Obullo\View\Gui\ViewComponent;
use Obullo\View\TemplateInterface;
use Obullo\Container\ContainerAwareTrait;

class IndexTemplate implements TemplateInterface
{
    use ContainerAwareTrait;

    /**
     * Template name
     * 
     * @var string
     */
    protected $name;

    /**
     * Template variables
     * 
     * @var array
     */
    protected $variables = array();

    /**
     * Constructor
     * 
     * @param string $name name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns to template name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set template variables & create view components
     * 
     * @return void
     */
    public function setVariables()
    {
        $this->variables['header'] = $this->getHeaderComponent();
        $this->variables['footer'] = $this->getFooterComponent();
    }

    /**
     * Returns to template variables
     * 
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Create header component
     * 
     * @return string
     */
    protected function getHeaderComponent()
    {
        $header = new ViewComponent('header');
        $result = $this->getContainer()->get('mvc')->get($header);

        return $result;
    }

    /**
     * Create footer component
     * 
     * @return string
     */
    protected function getFooterComponent()
    {
        $footer = new ViewComponent('footer');
        $result = $this->getContainer()->get('mvc')->get($footer);
        
        return $result;
    }

}