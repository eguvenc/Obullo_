<?php

namespace Obullo\View;

use Closure;
use Obullo\Mvc\Controller;
use Psr\Log\LoggerInterface as Logger;
use Obullo\View\Model\ViewModelInterface as ViewModel;
use Interop\Container\ContainerInterface as Container;

/**
 * View Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class View implements ViewInterface
{
    /**
     * Logger
     * 
     * @var object
     */
    protected $logger;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Data
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params = array();

    /**
     * View folders
     * 
     * @var array
     */
    protected $folders = array();

    /**
     * File extension
     * 
     * @var string
     */
    protected $fileExtension = null;

    /**
     * Constructor
     * 
     * @param object $container container
     * @param object $logger    logger
     * @param array  $params    service provider parameters
     */
    public function __construct(Container $container, $logger, array $params)
    {
        $this->container = $container;
        $this->params = $params;
        $this->logger = $logger;
        $this->logger->debug('View Class Initialized');
    }

    /**
     * Set the template file extension.
     * 
     * @param string|null $fileExtension Pass null to manually set it.+
     * 
     * @return Engine
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }

    /**
     * Register view folder
     * 
     * @param string $name folder name
     * @param string $path folder path
     *
     * @return void
     */
    public function addFolder($name, $path = null)
    {
        $this->folders[$name] = $path;
    }

    /**
     * Check folders & returns to array if yes.
     *
     * @return boolean
     */
    public function getFolders()
    {
        return (empty($this->folders)) ? false : $this->folders;
    }

    /**
     * Include nested view files from current module /view folder
     * 
     * @param mixed $filename filename
     * @param mixed $data     array data
     * 
     * @return string                      
     */
    public function render($filename, $data = array())
    {
        if ($filename instanceof ViewModel) {
            
            $data     = $filename->getVariables();
            $template = $filename->getTemplate();

            if ($template instanceof TemplateInterface) {
                $template->setContainer($this->container);
                $template->setVariables();

                $filename = $template->getName();
                $data     = array_merge($template->getVariables(), $data);

                // print_r($data);

            } else {
                $filename = (string)$template;
            }
        }
        return $this->renderView($filename, $data, true);
    }

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = array())
    {
        return $this->renderView($filename, $data, false);
    }

    /**
     * Set view variables
     * 
     * @param string $key key
     * @param mixed  $val val
     */
    public function __set($key, $val)
    {
        $this->data($key, $val);
    }

    /**
     * Set variables
     * 
     * @param string $key view key data
     * @param mixed  $val mixed
     * 
     * @return object
     */
    public function data($key, $val = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $val;
        }
        return $this;
    }

    /**
     * Render nested view files
     * 
     * @param string  $filename filename
     * @param mixed   $data     array data
     * @param boolean $include  fetch as string or return
     * 
     * @return object Stream or Response                     
     */
    protected function renderView($filename, $data = array(), $include = true)
    {
        /**
         * IMPORTANT:
         * 
         * Router may not available in some levels, forexample if we define a closure route 
         * which contains view class, it will not work if router not available in the controller.
         * So first we need check Controller is available if not we use container->router.
         */
        // if (! class_exists('Obullo\Mvc\Controller', false) || Controller::$instance == null) {
        //     $router = $this->container->get('router');
        // } else {
        //     $router = &Controller::$instance->router;  // Use nested controller router ( @see the Layer package. )
        // }
        
        // $path   = $router->getAncestor('/') . $router->getFolder();
        // $folder = (empty($path)) ? APP .'View' : CONTROLLER . $path .'/View';

        $folder = APP . 'View';

        /**
         * End layer package support
         */
        $body = $this->renderFile($filename, $folder, $data);

        if ($include === false) {
            return $body;
        }
        $response = $this->container->get('response');
        $response->getBody()->write($body);

        return $response;
    }

    /**
     * Render view
     * 
     * @param string $filename filename
     * @param string $path     path
     * @param array  $data     data
     * 
     * @return string
     */
    protected function renderFile($filename, $path, $data = array())
    {
        $data = array_merge($this->data, $data);

        $engineClass = "\\".trim($this->params['engine'], '\\');
        $engine = new $engineClass($path);

        $engine->setFileExtension($this->fileExtension);
        $engine->setContainer($this->container);

        if ($folders = $this->getFolders()) {
            foreach ($folders as $name => $folder) {
                $engine->addFolder($name, $folder);
            }
        }
        return $engine->render($filename, $data);
    }

}