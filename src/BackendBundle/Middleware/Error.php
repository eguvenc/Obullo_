<?php

namespace BackendBundle\Middleware;

use Exception;
use RuntimeException;
use Zend\Diactoros\Response\HtmlResponse;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Error implements ErrorMiddlewareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Invoke middleware
     *
     * @param mixed    $err      string error or object of Exception
     * @param Request  $request  Psr\Http\Message\ServerRequestInterface
     * @param Response $response Psr\Http\Message\ResponseInterface
     *
     * @return object response
     */
    public function __invoke($err, Request $request, Response $response)
    {
        return $this->handleError($err);
    }
    
    /**
     * Handle application errors
     *
     * @param mixed $err mostly exception object
     *
     * @return object response
     */
    protected function handleError($err)
    {
        $html = $this->renderHtmlErrorMessage($err);
        // $json = $this->renderJsonErrorMessage($error);
        
        if (is_object($err)) {
            switch ($err) {
                case ($err instanceof Exception):
                case ($err instanceof RuntimeException):
                    // error log
                    break;
            }
        }
        // return new JsonResponse($json, 500, [], JSON_PRETTY_PRINT);

        return new HtmlResponse($html, 500);
    }

    /**
     * Render HTML error page
     *
     * @param error $error error | exception
     *
     * @return string
     */
    protected function renderHtmlErrorMessage($error)
    {
        $html  = null;
        $title = 'Application Error';

        if (is_string($error)) {
            $html = $error;
        } elseif (is_object($error)) {
            $html = $this->renderHtmlException($error);
            while ($exception = $error->getPrevious()) {
                $html .= '<h2>Previous exception</h2>';
                $html .= $this->renderHtmlException($exception);
            }
        }
        $header = '<style>
        body{ color: #777575 !important; margin:0 !important; padding:20px !important; font-family:Arial,Verdana,sans-serif !important;font-weight:normal;  }
        h1, h2, h3, h4 {
            margin: 0;
            padding: 0;
            font-weight: normal;
            line-height:48px;
        }
        </style>';

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title>%s</head><body><h1>%s</h1>%s</body></html>",
            $title,
            $header,
            $title,
            $html
        );

        return $output;
    }

    /**
     * Render exception as HTML.
     *
     * @param Exception $exception exception
     *
     * @return string
     */
    protected function renderHtmlException(Exception $exception)
    {
        $html = sprintf('<tr><td><strong>Type:</strong></td><td>%s</td></tr>', get_class($exception));

        if (($message = $exception->getMessage())) {
            $html .= sprintf('<tr><td><strong>Message:</strong></td><td>%s</td></tr>', $message);
        }

        if (($code = $exception->getCode())) {
            $html .= sprintf('<tr><td><strong>Code:</strong></td><td>%s</td></tr>', $code);
        }

        if (($file = $exception->getFile())) {
            $html .= sprintf('<tr><td><strong>File:</strong></td><td>%s</td></tr>', $file);
        }

        if (($line = $exception->getLine())) {
            $html .= sprintf('<tr><td><strong>Line:</strong></td><td>%s</td></tr>', $line);
        }
        $html = "<table>".$html."</table>";

        if (($trace = $exception->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
        }
        
        return $html;
    }

    /**
     * Render JSON error
     *
     * @param Exception $exception exception
     *
     * @return string
     */
    protected function renderJsonErrorMessage(Exception $exception)
    {
        $error = [
            "success" => 0,
            'message' => 'Rest Api Error',
        ];
        $error['exception'] = [];

        do {
            $error['exception'][] = [
                'type' => get_class($exception),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        } while ($exception = $exception->getPrevious());
    
        return $error;
    }
}
