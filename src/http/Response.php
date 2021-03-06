<?php

namespace toom1996\http;

use toom1996\base\Component;
use toom1996\http\HttpException;
use toom1996\base\NotFoundHttpException;
use toom1996\web\HeaderCollection;
use yii\http\HeadersAlreadySentException;

/**
 * Class Response
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Response extends Component
{

    public $fd = 0;

    public $socket;

    public $header;

    public $cookie;

    public $trailer;

    public $content;

    public $format = self::FORMAT_HTML;

    /**
     * @var
     */
    public $stream;

    /**
     * Swoole response.
     * @var \Swoole\Http\Response
     */
    public $response;

    /**
     * Response headers.
     * @var
     */
    private $_headers = [];

    /**
     * Whether is send.
     * @var
     */
    public $isSend;

    /**
     * @var 
     */
    public $charset;


    const FORMAT_HTML = 'html';

    /**
     * @var array list of HTTP status codes and the corresponding texts
     */
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var int the HTTP status code to send with the response.
     */
    private $_statusCode = 200;

    public function __construct($config)
    {
        $this->response = $config;
    }

    /**
     * @var string the HTTP status description that comes together with the status code.
     * @see httpStatuses
     */
    public $statusText = 'OK';

    /**
     * Send response content.
     * @return bool
     */
    public function send()
    {
        if ($this->isSend) {

            return false;
        }
        $this->prepare();
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Set send header.
     */
    public function sendHeaders()
    {
        if ($this->_headers) {
            foreach ($this->getHeaders() as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                // set replace for first occurrence of header but false afterwards to allow multiple
                foreach ($values as $value) {
                    $this->response->header($name, $value);
                }
            }
        }
        //        $this->sendCookies();
    }

    public function prepare()
    {
        if ($this->_statusCode === 204) {
            $this->content = '';
            $this->stream = null;
            return;
        }

        if ($this->stream !== null) {
            return;
        }



    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function setHeader($header, $body)
    {

    }

    public function sendContent()
    {
        // Set isSend is true.
        // Prevent duplicate output.
        $this->isSend = true;
        if (!$this->content) {
            $this->content = ob_get_clean();
        }
        $this->response->setStatusCode($this->_statusCode);
        $this->response->end($this->content);
    }

    /**
     * Set http status code.
     * @param $value
     * @param  null  $text
     *
     * @return $this
     */
    public function setStatusCode($value, $text = null): Response
    {
        if ($value === null) {
            $value = 200;
        }
        $this->_statusCode = (int) $value;
        if ($this->getIsInvalid()) {
            // TODO throw new Exception.
            echo 'throw new Exception.';
        }
        if ($text === null) {
            $this->statusText = isset(static::$httpStatuses[$this->_statusCode]) ? static::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }

        return $this;
    }

    /**
     * @return bool whether this response has a valid [[statusCode]].
     */
    public function getIsInvalid()
    {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    /**
     * @return int the HTTP status code to send with the response.
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function redirect()
    {

    }

    /**
     * Set response content.
     * @param $content
     */
    public function setContent($content)
    {
        $this->content .= $content;
    }

    /**
     *
     * @param $exception \Exception
     *
     * @return $this
     */
    public function setStatusCodeByException($exception)
    {
        if ($exception instanceof HttpException) {
            $this->setStatusCode($exception->getCode());
        } else {
            $this->setStatusCode(500);
        }

        return $this;
    }
}