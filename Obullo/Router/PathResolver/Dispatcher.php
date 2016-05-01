<?php

namespace Router;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RouteDispatcher {

    public function __construct($handler)
    {
        $this->setRequest(explode("/", trim($handler, "/")));
    }

    /**
     * Detect class & method names
     *
     * This function takes an array of URI segments as
     * input, and sets the current class/method
     *
     * @param array $segments segments
     * 
     * @return void
     */
    public function setRequest($segments = array())
    {
        $resolver = $this->resolve($segments);
        if ($resolver == null) {
            return;
        }
        $arity    = $resolver->getArity();
        $segments = $resolver->getSegments();

        $class  = 1 + $arity;
        $method = 2 + $arity;

        if (! empty($segments[$class])) {
            $this->setClass($segments[$class]);
        }
        if (! empty($segments[$method])) {
            $this->setMethod($segments[$method]); // A standard method request
        } else {
            $segments[$method] = 'index';  // This lets the "routed" segment array identify 
                                           // that the default index method is being used.
            $this->setMethod('index');
        }
        $this->arity = (3 + $arity);
    }

    /**
     * Resolve segments
     * 
     * @param array $segments uri parts
     * 
     * @return array|null
     */
    protected function resolve($segments)
    {
        if (empty($segments[0])) {
            return null;
        }
        $this->setFolder($segments[0]);      // Set first segment as default folder
        $segments = $this->checkAncestor($segments);
        $ancestor = $this->getAncestor('/');

        if (! empty($ancestor)) {
            $resolver = new AncestorResolver($this);
            return $resolver->resolve($segments);
        }
        if (is_dir(FOLDERS .$this->getFolder())) {
            $resolver = new FolderResolver($this);
            return $resolver->resolve($segments);
        }
        $this->setFolder(null);
        $resolver = new ClassResolver($this);
        return $resolver->resolve($segments);
    }

    /**
     * Check first segment if have a ancestor folder & set it.
     * 
     * @param array $segments uri segments
     * 
     * @return array
     */
    protected function checkAncestor($segments)
    {
        if (! empty($segments[1])
            && strtolower($segments[1]) != 'views'  // http://example/debugger/view/index bug fix
            && is_dir(FOLDERS .$segments[0].'/'. $segments[1].'/')  // Detect ancestor folder and change folder !!
        ) {
            $this->setAncestor($segments[0]);
            array_shift($segments);
        }
        return $segments;
    }

}
