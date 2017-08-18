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
     * Segments
     *
     * @var array
     */
    protected $segments;

    /**
     * Resolve
     *
     * @param array $segments uri segments
     *
     * @return array resolved segments
     */
    public function resolve(array $segments)
    {
        $folder = $segments[0];
        $hasSegmentOne = empty($segments[1]) ? false : true;

        $file = APP_PATH .'/Controller/'.$folder.'/'.$folder.'.php';

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
     * Returns to class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->segments[1];
    }

    /**
     * Returns to method name
     *
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->segments[2])) {  // default method
            return 'index';
        }
        return $this->segments[2];
    }
}
