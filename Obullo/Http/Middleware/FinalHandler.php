<?php

namespace Http\Middleware;

use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Exception;
use ErrorException;
use LogicException;
use RuntimeException;
use Container\ContainerAwareTrait;
use Container\ContainerAwareInterface;

class FinalHandler implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $app;

    /**
     * Request
     * 
     * @var object
     */
    protected $request;

    /**
     * Construct
     * 
     * @param object $app app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

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
        if ($err) {
            return $this->handleError($err);
        }
        if ($handler instanceof Response) {
            return $handler;
        }
        $result = $this->app->call($request, $response);

        if (! $result) {
            // $this->create404();
        }
        if ($result instanceof $response) {
            $response = $result;
        }

        // if (! $result) {

        //     $stream = new Stream(fopen('php://temp', 'r+'));
        //     $stream->write($this->container->get('view')->get('templates::404'));
                
        //     return $response
        //         ->withStatus(404)
        //         ->withHeader('Content-Type', 'text/html')
        //         ->withBody($stream);

        // }

        $this->request = $request;
        $response = $this->sendCookieHeaders($response);
        return $response;
    }

    /**
     * Set cookie headers
     * 
     * @param Response $response http ressponse
     *
     * @return object response
     */
    protected function sendCookieHeaders(Response $response)
    {
        if ($this->container->hasShared('cookie')) {

            $headers = $this->container->get('cookie')->getHeaders();
            if (! empty($headers)) {
                foreach ($headers as $value) {
                    // sprintf("%s: %s", 'Set-Cookie', $value);
                    $response = $response->withAddedHeader('Set-Cookie', $value);
                }
            }
            // if (! empty($headers)) {
            //     $response->setCookies($headers);
            //     return $response;
            // }
        }
        return $response;
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
            /*
            | Exception Hierarchy
            |
            |   - Exception
            |       - ErrorException
            |       - LogicException
            |           - BadFunctionCallException
            |               - BadMethodCallException
            |           - DomainException
            |           - InvalidArgumentException
            |           - LengthException
            |           - OutOfRangeException
            |       - RuntimeException
            |           - PDOException
            |           - OutOfBoundsException
            |           - OverflowException
            |           - RangeException
            |           - UnderflowException
            |           - UnexpectedValueException
            */
            switch ($err) {
            case ($err instanceof Exception):
            case ($err instanceof ErrorException):
            case ($err instanceof LogicException):
            case ($err instanceof RuntimeException):
                // $log = new Log($container->get('logger'));
                // $log->message($err);
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
        $title = 'Http Error';

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