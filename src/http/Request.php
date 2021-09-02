<?php

namespace toom1996\http;

use app\controllers\SiteController;
use FastRoute\Dispatcher;
use Swoole\Coroutine;
use toom1996\base\Component;
use toom1996\http\NotFoundHttpException;
use toom1996\http\MethodNotAllowedHttpException;

/**
 * Class Request
 *
 * @author TOOM <1023150697@qq.com>
 * @property integer $fd
 * @property string $streamId
 * @property array $header
 * @property array $server
 * @property string $cookie
 * @property array $get
 * @property string $files
 * @property array $post
 * @property string $tmpfiles
 */
class Request extends Component
{

    /**
     * Request path
     * @var
     */
    private $_pathInfo;

    /**
     * Query Params
     * @var
     */
    private $_queryParams;

    /**
     * Swoole fd
     * @var
     */
    public $fd;

    /**
     * Swoole stream id
     * @var
     */
    public $streamId;

    /**
     * Swoole request Header
     * @var
     */
    public $header;

    /**
     * Swoole request server
     * @var
     */
    public $server;

    /**
     * Swoole request cookie
     * @var
     */
    public $cookie;

    /**
     * Swoole request $_GET
     * @var
     */
    public $get;

    /**
     * Swoole request files
     * @var
     */
    public $files;

    /**
     * Swoole request $_POST
     * @var
     */
    public $post;

    /**
     * Swoole request temp files
     * @var
     */
    public $tmpfiles;

    /**
     * Request method.
     * @var
     */
    private $_method;

    public function __construct($id = null, $params = null)
    {
        parent::__construct($id, $params);
    }


    /**
     *
     * @return array
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\http\MethodNotAllowedHttpException
     * @throws \toom1996\http\NotFoundHttpException
     */
    public function resolve()
    {
        [$handler, $param] = Goblin::$app->getUrlManager()->parseRequest();
        return [$handler, array_merge($this->getQueryParams(), $param)];
    }


    /**
     * Returns the path info of the currently requested URL.
     * A path info refers to the part that is after the entry script and before the question mark (query string).
     * The starting and ending slashes are both removed.
     * @return string part of the request URL that is after the entry script and before the question mark.
     * Note, the returned path info is already URL-decoded.
     * @throws InvalidConfigException if the path info cannot be determined due to unexpected server configuration
     */
    public function getPathInfo()
    {
        if ($this->_pathInfo === null) {
            $this->_pathInfo = $this->server['path_info'];
        }

        return $this->_pathInfo;
    }

    /**
     * Resolves the request URI portion for the currently requested URL.
     * @return string|bool the request URI portion for the currently requested URL.
     * Note that the URI returned may be URL-encoded depending on the client.
     * @throws InvalidConfigException if the request URI cannot be determined due to unusual server configuration
     */
    public function getUrl()
    {
        $requestUri = $this->server['request_uri'];
        if ($requestUri !== '' && $requestUri[0] !== '/') {
            $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
        }
        return $requestUri;
    }

    /**
     * Returns the request parameters given in the [[queryString]].
     *
     * This method will return the contents of
     * This method will return the contents of swoole `$_GET` if params where not explicitly set.
     * @return array the request GET parameter values.
     * @see setQueryParams()
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            return $this->get ?? [];
        }

        return $this->_queryParams;
    }

    /**
     * Returns the request method given in the [[method]].
     *
     * Thid method will return the conentes of swoole `server['request_method']` if params where not explicitly set.
     * @return mixed
     */
    public function getMethod()
    {
        if ($this->_method === null) {
            return $this->server['request_method'];
        }

        return $this->_method;
    }

    /**
     * Returns GET parameter with a given name. If name isn't specified, returns an array of all GET parameters.
     *
     * @param string $name the parameter name
     * @param mixed $defaultValue the default parameter value if the parameter does not exist.
     * @return array|mixed
     */
    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter passed to this method will be returned.
     * @param string $name the GET parameter name.
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getBodyParam()
     */
    public function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }
    
    
}