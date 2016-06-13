<?php

namespace Obullo;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Obullo\Router\Resolver\ClassResolver;
use Obullo\Router\Resolver\FolderResolver;
use Obullo\Router\Resolver\AncestorResolver;

/**
 * Resolve controller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ControllerResolver
{
    protected $router;
    protected $folder = 'controllers';
    protected $subfolderLevel;

    /**
     * Constructor
     * 
     * @param Container $container container
     * @param Request   $request   request
     * @param Response  $response  response
     */
    public function __construct($container, $request, $response)
    {
        $container->share('response', $response);  // Refresh objects
        $container->share('request', $request);

        $this->router = $container->get('router');
        $this->container = $container;
    }

    /**
     * Detect class & method names
     *
     * This function takes an array of URI segments as
     * input, and sets the current class/method
     *
     * @param string $handler path
     * 
     * @return boolean|null
     */
    public function dispatch($handler)
    {
        $resolver = $this->resolve(explode("/", trim($handler, "/")));
        if ($resolver == null) {
            return;
        }
        $arity    = $resolver->getArity();
        $segments = $resolver->getSegments();

        $class  = 1 + $arity;
        $method = 2 + $arity;

        if (! empty($segments[$class])) {
            $this->router->setClass($segments[$class]);
        }
        if (! empty($segments[$method])) {
            $this->router->setMethod($segments[$method]); // A standard method request
        } else {
            $segments[$method] = 'index';  // This lets the "routed" segment array identify 
                                           // that the default index method is being used.
            $this->router->setMethod('index');
        }
        $this->arity = (3 + $arity);

        return $this->call($segments);
    }

    /**
     * Set dispatch folder
     * 
     * @param string $folder folder
     *
     * @return void
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Set sub directory level
     * 
     * @param int $level level
     *
     * @return object
     */
    public function setSubfolderLevel($level)
    {
        $this->subfolderLevel = (int)$level;
        return $this;
    }

    /**
     * Returns to subfolder level
     * 
     * @return integer
     */
    public function getSubfolderLevel()
    {
        return $this->subfolderLevel;
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
        $this->router->setFolder($segments[0]);      // Set first segment as default folder
        $segments = $this->checkAncestor($segments);
        $ancestor = $this->router->getAncestor('/');

        if (! empty($ancestor)) {
            $resolver = new AncestorResolver($this->router, $this->getSubfolderLevel());
            return $resolver->resolve($segments);
        }
        if (is_dir(APP .$this->folder.'/'.$this->router->getFolder())) {
            $resolver = new FolderResolver($this->router);
            return $resolver->resolve($segments);
        }
        $this->router->setFolder(null);
        $resolver = new ClassResolver($this->router);
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
            && is_dir(APP .$this->folder.'/'.$segments[0].'/'. $segments[1].'/')  // Detect ancestor folder and change folder !!
        ) {
            $this->router->setAncestor($segments[0]);
            array_shift($segments);
        }
        return $segments;
    }

    /**
     * Returns to arity
     * 
     * @return integer
     */
    public function getArity()
    {
        return $this->arity;
    }

    /**
     * Returns to called filename
     * 
     * @return string
     */
    public function getFilename()
    {
        return APP .$this->folder.'/'.$this->router->getAncestor('/').$this->router->getFolder('/').$this->router->getClass().'.php';
    }

    /**
     * Returns to called filename of namespace
     * 
     * @return string
     */
    public function getNamespace()
    {
        return '\\'.$this->router->getNamespace().$this->router->getClass();
    }

    /**
     * Call controller
     *
     * @param array $segments path segments
     * 
     * @return mixed
     */
    public function call($segments)
    {
        $file      = $this->getFilename();
        $className = $this->getNamespace();

        if (! is_file($file)) {

            $this->router->clear();  // Fix layer errors.
            return false;

        } else {

            if (! class_exists($className, false)) {  // Hmvc request support
                include $file;
            }
            $controller = new $className($this->container);
            $controller->container = $this->container;

            if (! method_exists($controller, $this->router->getMethod())
                || substr($this->router->getMethod(), 0, 1) == '_'
            ) {
                $this->router->clear();  // Fix layer errors.
                return false;
            }
        }
        $result = call_user_func_array(
            array(
                $controller,
                $this->router->getMethod()
            ),
            array_slice($segments, $this->getArity())
        );
        if ($result instanceof Response) {
            return $result;
        }
        return true;
    }


}