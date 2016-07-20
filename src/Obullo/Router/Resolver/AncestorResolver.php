<?php

namespace Obullo\Router\Resolver;

use Obullo\Utils\Route as RouteHelper;
use Obullo\Router\RouterInterface as Router;

/**
 * Resolve primary folder
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AncestorResolver
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Argument slice
     * 
     * @var integer
     */
    protected $arity = 0;

    /**
     * Segments
     * 
     * @var array
     */
    protected $segments;

    /**
     * Subfolder level
     * 
     * @var int
     */
    protected $subfolderLevel;

    /**
     * Constructor
     * 
     * @param Router  $router         router
     * @param Integer $subfolderLevel integer
     */
    public function __construct(Router $router, $subfolderLevel)
    {
        $this->router  = $router;
        $this->subfolderLevel = (int)$subfolderLevel;
    }

    /**
     * Resolve
     * 
     * @param array $segments uri segments
     * 
     * @return array resolved segments
     */
    public function resolve(array $segments)
    {
        $ancestor   = $this->router->getAncestor('/');
        $CONTROLLER = $this->getSubfolder($ancestor, $segments);

        $this->router->setFolder(implode("/", $CONTROLLER));

        $folder = $this->router->getFolder();
        $arity  = count($CONTROLLER) -1;

        // Rewrite support "/examples/forms" to "/examples/forms/forms"
        
        if (empty($segments[1])) {
            $segments[1] = $folder;
        }
        $file = APP_PATH .'Controller/'.$ancestor.$folder.'/'.RouteHelper::ucwords($segments[1]) .'.php';

        // Support for e.g "/examples/forms/Ajax"
    
        if (is_file($file)) {
            $this->segments = $segments;
            return $this;
        } elseif ($segments[1] == $folder) {
            unset($segments[1]);  // Remove segment[1] before we added it for url rewriting
        }

        // Support for unlimited subCONTROLLERS

        if (isset($segments[2]) && is_dir(APP_PATH .'Controller/'.$ancestor.$folder)) {
            $this->arity = $arity;
            $this->segments = $segments;
            return $this;
        }
        $this->segments = $segments;
        return $this;
    }

    /**
     * Returns to sub CONTROLLERS if they exist
     * 
     * @param string $ancestor ancestor folder
     * @param array  $segments uri segments
     * 
     * @return array
     */
    protected function getSubfolder($ancestor, $segments)
    {
        $append  = "";
        $temp = [];
        foreach ($segments as $key => $folder) {

            if ($key > $this->subfolderLevel) {  // Subfolder level limit
                continue;
            }
            if (isset($temp[$key - 1])) {
                $append = $temp[$key - 1];
            }
            if (is_dir(APP_PATH .'/Controller/'.$ancestor.$append."/".$folder)) {
                $temp[$key]    = $append ."/". $folder;
                $CONTROLLERS[$key] = $folder;
            }
        }
        return $CONTROLLERS;
    }

    /**
     * Get arity
     * 
     * @return int
     */
    public function getArity()
    {
        return $this->arity;
    }

    /**
     * Get uri segments
     * 
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

}