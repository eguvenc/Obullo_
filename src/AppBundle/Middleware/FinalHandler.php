<?php

namespace AppBundle\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Mvc\ControllerResolver;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

class FinalHandler implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Request
     *
     * @var object
     */
    protected $request;

    /**
     * Invoke middleware
     *
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param Exception              $err      error
     * @param Callable               $handler  handler
     *
     * @return object ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $err = null, $handler = null)
    {
        if ($handler instanceof Response) {
            return $handler;
        }
        $resolver = new ControllerResolver($this->container, $request, $response);
        $result   = $resolver->dispatch($handler);

        if (! $result) {
            return $this->create404($response);
        }
        if ($result instanceof $response) {
            $response = $result;
        }
        $this->request = $request;
        return $response;
    }
    
}
