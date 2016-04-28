<?php

namespace Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Exception;
use Container\ContainerAwareTrait;
use Container\ContainerAwareInterface;

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
     * 
     * @return object ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {   
        $this->request = $request;
        
        $response = $this->setCookies($response);

        return $response;
    }

    /**
     * Set cookie headers
     * 
     * @param Response $response http ressponse
     *
     * @return object response
     */
    protected function setCookies(Response $response)
    {
        if ($this->container->hasShared('cookie')) {

            $headers = $this->container->get('cookie')->getHeaders();

            if (! empty($headers)) {
                $response->setCookies($headers);
                return $response;
            }
        }
        return $response;
    }

}