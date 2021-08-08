<?php

namespace toom1996\web;

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
        $this->renderException($this->_errorData);
    }

    protected function renderException($exception)
    {
        // TODO: Implement renderException() method.

        if (\YiiS::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        $response->setStatusCodeByException($exception);

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);

        if ($useErrorView && $this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
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

        $response->send();
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

}