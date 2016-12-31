<?php

namespace BackendBundle\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface ErrorMiddlewareInterface
{
    /**
     * Process an incoming error, along with associated request and response.
     *
     * Accepts an error, a server-side request, and a response instance, and
     * does something with them; if further processing can be done, it can
     * delegate to `$out`.
     *
     * @param mixed    $err      error
     * @param Request  $request  request
     * @param Response $response response
     *
     * @return null|Response
     */
    public function __invoke($err, Request $request, Response $response);
}
