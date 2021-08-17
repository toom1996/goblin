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

}