<?php

namespace toom1996\log;

use toom1996\base\Component;
use toom1996\helpers\BaseVarDumper;
use toom1996\http\Goblin;


abstract class Target extends Component
{
    /**
     * @var
     */
    public $logFile;

    /**
     * Is append microtime to log time.
     * @var bool
     */
    public $microtime = false;

    public $logVars = [

    ];

    /**
     * Log content.
     * @var array
     */
    public $messages = [];

    /**
     * @var int how many messages should be logged before they are flushed from memory and sent to targets.
     * Defaults to 1000, meaning the [[flush]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[flush()]].
     */
    public $flushInterval = 500;

    abstract public function export();


    public function flush()
    {
        $count = count($this->messages);
        if ($count > 0) {
            $this->export();
            $this->messages = [];
        }
    }

    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = LogDispatcher::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text =  BaseVarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        return $this->getTime($timestamp) . " [$level] $text"
            . (empty($traces) ? '' : "\n    " . implode("\n    ", $traces));
    }

    protected function getTime($timestamp)
    {
        $parts = explode('.', sprintf('%F', $timestamp));

        return date('Y-m-d H:i:s', $parts[0]) . ($this->microtime ? ('.' . $parts[1]) : '');
    }
}