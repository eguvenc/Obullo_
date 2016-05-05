<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve folder
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class FolderResolver
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

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
        $folder = $this->router->getFolder();
        $hasSegmentOne = empty($segments[1]) ? false : true;

        $file = CONTROLLERS .$folder.'/'.$this->router->ucwordsUnderscore($folder).'.php';

        if (is_file($file)) {

            $index = ($hasSegmentOne && $segments[1] == 'index');

            if ($hasSegmentOne == false || $index) {  // welcome/hello support
                array_unshift($segments, $folder);
            }
            $this->segments = $segments;
            return $this;
        }
        $this->segments = $segments;
        return $this;
    }

    /**
     * Get segment factor
     * 
     * @return int
     */
    public function getArity()
    {
        return 0;
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