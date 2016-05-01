<?php

namespace Router\Resolver;

use Router\RouterInterface as Router;

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
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
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
        $ancestor = $this->router->getAncestor('/');
        $folders  = $this->getSubfolders($ancestor, $segments);

        $this->router->setFolder(implode("/", $folders));

        $folder = $this->router->getFolder();
        $arity  = count($folders) -1;

        // Rewrite support "/examples/forms" to "/examples/forms/forms"
        
        if (empty($segments[1])) {
            $segments[1] = $folder;
        }
        $file = FOLDERS .$ancestor.$folder.'/'.$this->router->ucwordsUnderscore($segments[1]).'.php';

        // Support for e.g "/examples/forms/Ajax"
    
        if (is_file($file)) {
            $this->segments = $segments;
            return $this;
        } elseif ($segments[1] == $folder) {
            unset($segments[1]);  // Remove segment[1] before we added it for url rewriting
        }

        // Support for unlimited subfolders

        if (isset($segments[2]) && is_dir(FOLDERS .$ancestor.$folder)) {
            $this->arity = $arity;
            $this->segments = $segments;
            return $this;
        }
        $this->segments = $segments;
        return $this;
    }

    /**
     * Returns to sub folders if they exist
     * 
     * @param string $ancestor ancestor folder
     * @param array  $segments uri segments
     * 
     * @return array
     */
    protected function getSubfolders($ancestor, $segments)
    {
        $append  = "";
        $temp = [];
        foreach ($segments as $key => $folder) {

            if ($key > $this->router->getSubfolderLevel()) {  // Subfolder level limit
                continue;
            }
            if (isset($temp[$key - 1])) {
                $append = $temp[$key - 1];
            }
            if (is_dir(FOLDERS .$ancestor.$append."/".$folder)) {
                $temp[$key]    = $append."/".$folder;
                $folders[$key] = $folder;
            }
        }
        return $folders;
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