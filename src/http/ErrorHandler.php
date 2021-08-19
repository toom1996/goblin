<?php

namespace toom1996\http;

use toom1996\base\Exception;
use toom1996\base\InvalidCallException;
use toom1996\http\Response;

class ErrorHandler extends \toom1996\base\ErrorHandler
{
    /**
     * ErrorHandler render error page action.
     * @var
     */
    public $errorAction;

    /**
     * @var
     */
    public $exception;

    private $_errorData;

    public $maxSourceLines = 19;

    public $maxTraceSourceLines = 13;

    public $previousExceptionView = '@app/views/site/previousException.php';

    public $callStackItemView = '@app/views/site/callStackItem.php';

    public $traceLine = '{html}';

    /**
     *
     * @param $exception \Exception
     */
    public function handleException($exception)
    {
        $this->exception = $exception;
        $this->_errorData = [
            'type' => get_class($exception),
            'file' => method_exists($exception, 'getFile') ? $exception->getFile() : '',
            'errorMessage' => $exception->getMessage(),
            'line' => $exception->getLine(),
            'stack-trace' => explode("\n", $exception->getTraceAsString()),
        ];
        var_dump($this->_errorData);
        $this->renderException($exception);
    }

    /**
     *
     * @param $exception \Exception
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     */
    protected function renderException($exception)
    {
        // TODO: Implement renderException() method.

        try {
            if (Goblin::$app->has('response')) {
                $response = Goblin::$app->getResponse();
//            $response->isSend = false;
                $response->content = null;
            } else {
                $response = Goblin::$app->get('response');
            }
            $response->setStatusCodeByException($exception);

            $useErrorView = $response->format === Response::FORMAT_HTML;
            if ($useErrorView && $this->errorAction !== null) {
                $result = Goblin::$app->runAction($this->errorAction);
                $response->content = $result;
            } elseif ($response->format === Response::FORMAT_HTML) {
                if ($this->shouldRenderSimpleHtml()) {
                    // AJAX request
                    $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
                } else {
                    // if there is an error during error rendering it's useful to
                    // display PHP error in debug mode instead of a blank screen
                    if (YII_DEBUG) {
                        ini_set('display_errors', 1);
                    }
                    $file = $useErrorView ? $this->errorView : $this->exceptionView;
                    $response->data = $this->renderFile($file, [
                        'exception' => $exception,
                    ]);
                }
            } elseif ($response->format === Response::FORMAT_RAW) {
                $response->data = static::convertExceptionToString($exception);
            } else {
                $response->data = $this->convertExceptionToArray($exception);
            }
        }catch (\Throwable $e) {
            var_dump($e);
            $response->content = $e->getMessage();
        }
    }

    /**
     * Return exception.
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Return errorData.
     * @return mixed
     */
    public function getErrorData()
    {
        return $this->_errorData;
    }

    public function htmlEncode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    public function getExceptionName($exception)
    {
        if ($exception instanceof Exception || $exception instanceof InvalidCallException) {
            return $exception->getName();
        }

        return null;
    }

    public function createHttpStatusLink($statusCode, $statusDescription)
    {
        return '<a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#' . (int) $statusCode . '" target="_blank">HTTP ' . (int) $statusCode . ' &ndash; ' . $statusDescription . '</a>';
    }

    public function renderPreviousExceptions($exception)
    {
        if (($previous = $exception->getPrevious()) !== null) {
            return $this->renderFile($this->previousExceptionView, ['exception' => $previous]);
        }

        return '';
    }

    public function renderCallStack($exception)
    {
        $out = '<ul>';
        $out .= $this->renderCallStackItem($exception->getFile(), $exception->getLine(), null, null, [], 1);
        for ($i = 0, $trace = $exception->getTrace(), $length = count($trace); $i < $length; ++$i) {
            $file = !empty($trace[$i]['file']) ? $trace[$i]['file'] : null;
            $line = !empty($trace[$i]['line']) ? $trace[$i]['line'] : null;
            $class = !empty($trace[$i]['class']) ? $trace[$i]['class'] : null;
            $function = null;
            if (!empty($trace[$i]['function']) && $trace[$i]['function'] !== 'unknown') {
                $function = $trace[$i]['function'];
            }
            $args = !empty($trace[$i]['args']) ? $trace[$i]['args'] : [];
            $out .= $this->renderCallStackItem($file, $line, $class, $function, $args, $i + 2);
        }
        $out .= '</ul>';
        return $out;
    }

    public function renderCallStackItem($file, $line, $class, $method, $args, $index)
    {
        $lines = [];
        $begin = $end = 0;
        if ($file !== null && $line !== null) {
            $line--; // adjust line number from one-based to zero-based
            $lines = @file($file);
            if ($line < 0 || $lines === false || ($lineCount = count($lines)) < $line) {
                return '';
            }

            $half = (int) (($index === 1 ? $this->maxSourceLines : $this->maxTraceSourceLines) / 2);
            $begin = $line - $half > 0 ? $line - $half : 0;
            $end = $line + $half < $lineCount ? $line + $half : $lineCount - 1;
        }

        return $this->renderFile($this->callStackItemView, [
            'file' => $file,
            'line' => $line,
            'class' => $class,
            'method' => $method,
            'index' => $index,
            'lines' => $lines,
            'begin' => $begin,
            'end' => $end,
            'args' => $args,
        ]);
    }

    public function renderFile($_file_, $_params_)
    {
        $_params_['handler'] = $this;
        if ($this->exception instanceof \ErrorException || !Goblin::$app->has('view')) {
            ob_start();
            ob_implicit_flush(false);
            extract($_params_, EXTR_OVERWRITE);
            require Goblin::getAlias($_file_);

            return ob_get_clean();
        }

        $view = Goblin::$app->getView();

        return $view->renderFile($_file_, $_params_, $this);
    }

    public function isCoreFile($file)
    {
        return $file === null || strpos(realpath($file), APP_PATH . DIRECTORY_SEPARATOR) === 0;
    }

    protected function getTypeUrl($class, $method)
    {
        if (strncmp($class, 'yii\\', 4) !== 0) {
            return null;
        }

        $page = $this->htmlEncode(strtolower(str_replace('\\', '-', $class)));
        $url = "http://www.yiiframework.com/doc-2.0/$page.html";
        if ($method) {
            $url .= "#$method()-detail";
        }

        return $url;
    }

    public function addTypeLinks($code)
    {
        if (preg_match('/(.*?)::([^(]+)/', $code, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
            $text = $this->htmlEncode($class) . '::' . $this->htmlEncode($method);
        } else {
            $class = $code;
            $method = null;
            $text = $this->htmlEncode($class);
        }

        $url = null;

        $shouldGenerateLink = true;
        if ($method !== null && substr_compare($method, '{closure}', -9) !== 0) {
            $reflection = new \ReflectionClass($class);
            if ($reflection->hasMethod($method)) {
                $reflectionMethod = $reflection->getMethod($method);
                $shouldGenerateLink = $reflectionMethod->isPublic() || $reflectionMethod->isProtected();
            } else {
                $shouldGenerateLink = false;
            }
        }

        if ($shouldGenerateLink) {
            $url = $this->getTypeUrl($class, $method);
        }

        if ($url === null) {
            return $text;
        }

        return '<a href="' . $url . '" target="_blank">' . $text . '</a>';
    }

    public function argumentsToString($args)
    {
        $count = 0;
        $isAssoc = $args !== array_values($args);

        foreach ($args as $key => $value) {
            $count++;
            if ($count >= 5) {
                if ($count > 5) {
                    unset($args[$key]);
                } else {
                    $args[$key] = '...';
                }
                continue;
            }

            if (is_object($value)) {
                $args[$key] = '<span class="title">' . $this->htmlEncode(get_class($value)) . '</span>';
            } elseif (is_bool($value)) {
                $args[$key] = '<span class="keyword">' . ($value ? 'true' : 'false') . '</span>';
            } elseif (is_string($value)) {
                $fullValue = $this->htmlEncode($value);
                if (mb_strlen($value, 'UTF-8') > 32) {
                    $displayValue = $this->htmlEncode(mb_substr($value, 0, 32, 'UTF-8')) . '...';
                    $args[$key] = "<span class=\"string\" title=\"$fullValue\">'$displayValue'</span>";
                } else {
                    $args[$key] = "<span class=\"string\">'$fullValue'</span>";
                }
            } elseif (is_array($value)) {
                $args[$key] = '[' . $this->argumentsToString($value) . ']';
            } elseif ($value === null) {
                $args[$key] = '<span class="keyword">null</span>';
            } elseif (is_resource($value)) {
                $args[$key] = '<span class="keyword">resource</span>';
            } else {
                $args[$key] = '<span class="number">' . $value . '</span>';
            }

            if (is_string($key)) {
                $args[$key] = '<span class="string">\'' . $this->htmlEncode($key) . "'</span> => $args[$key]";
            } elseif ($isAssoc) {
                $args[$key] = "<span class=\"number\">$key</span> => $args[$key]";
            }
        }

        return implode(', ', $args);
    }

}