<?php

namespace BackendBundle\Middleware;

use Exception;
use Zend\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotFound
{
    /**
     * Invoke middleware
     *
     * @param Request  $request  Psr\Http\Message\ServerRequestInterface
     * @param Response $response Psr\Http\Message\ResponseInterface
     *
     * @return object response
     */
    public function __invoke(Request $request, Response $response)
    {
        $html = '<html>
        <head>
        <title>404 Page Not Found</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <style type="text/css">
        body{ color: #777575 !important; margin:0 !important; padding:20px !important; font-family:Arial,Verdana,sans-serif !important;font-weight:normal;  }
        h1, h2, h3, h4 {
            margin: 0;
            padding: 0;
            font-weight: normal;
            line-height:48px;
        }
        </style>
        </head>
        <body>

        <h1>404 Not Found</h1>
        <p>The page you are looking for could not be found.</p>

        </body>
        </html>';

        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($html);
                
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($stream);
    }
}
