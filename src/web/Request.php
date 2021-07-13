<?php

namespace toom1996\web;

use toom1996\base\Component;

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
     * 将当前请求解析为路由和相关参数。
     * Resolves the current request into a route and the associated parameters.
     * @return array the first element is the route, and the second is the associated parameters.
     */
    public function resolve()
    {
        $result = Toom::$app->getUrlManager()->parseRequest($this);
        if ($result !== false) {
            return [$result, $this->getQueryParams()];
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
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
            $this->_pathInfo = $this->resolvePathInfo();
        }

        return $this->_pathInfo;
    }

    public function resolvePathInfo()
    {
        $pathInfo = $this->getUrl();


        $pathInfo = urldecode($pathInfo);

        // 不知道是干嘛的, 编码??
        // try to encode in UTF8 if not so
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (!preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo)
        ) {
            $pathInfo = $this->utf8Encode($pathInfo);
        }
        var_dump($pathInfo);
        return (string) $pathInfo;
    }

    /**
     * 解析当前请求URL的请求URI部分。
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
     * 看不懂..
     * Encodes an ISO-8859-1 string to UTF-8
     * @param string $s
     * @return string the UTF-8 translation of `s`.
     * @see https://github.com/symfony/polyfill-php72/blob/master/Php72.php#L24
     */
    private function utf8Encode($s)
    {
        $s .= $s;
        $len = \strlen($s);
        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
                case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
                default: $s[$j] = "\xC3"; $s[++$j] = \chr(\ord($s[$i]) - 64); break;
            }
        }
        return substr($s, 0, $j);
    }

    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     * @throws InvalidConfigException if unable to determine the entry script URL
     */
    public function getScriptUrl()
    {
        if ($this->_scriptUrl === null) {
            $scriptFile = $this->getScriptFile();
            $scriptName = basename($scriptFile);
            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && ($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->_scriptUrl = str_replace([$_SERVER['DOCUMENT_ROOT'], '\\'], ['', '/'], $scriptFile);
            } else {
                throw new InvalidConfigException('Unable to determine the entry script URL.');
            }
        }

        return $this->_scriptUrl;
    }

    /**
     * Returns the request parameters given in the [[queryString]].
     *
     * This method will return the contents of `$_GET` if params where not explicitly set.
     * @return array the request GET parameter values.
     * @see setQueryParams()
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            return $this->get;
        }

        return $this->_queryParams;
    }
}