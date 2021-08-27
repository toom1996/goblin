<?php


namespace toom1996\log;


use toom1996\base\Component;
use toom1996\http\Goblin;

class Log extends Component
{
    /**
     * @var array logged messages. This property is managed by [[log()]] and [[flush()]].
     * Each log message is of the following structure:
     *
     * ```
     * [
     *   [0] => message (mixed, can be a string or some complex data, such as an exception object)
     *   [1] => level (integer)
     *   [2] => category (string)
     *   [3] => timestamp (float, obtained by microtime(true))
     *   [4] => traces (array, debug backtrace, contains the application code call stacks)
     *   [5] => memory usage in bytes (int, obtained by memory_get_usage()), available since version 2.0.11.
     * ]
     * ```
     */
    public $messages = [];
    /**
     * @var int how many messages should be logged before they are flushed from memory and sent to targets.
     * Defaults to 1000, meaning the [[flush]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[flush()]].
     */
    public $flushInterval = 1000;
    /**
     * @var int how much call stack information (file name and line number) should be logged for each message.
     * If it is greater than 0, at most that number of call stacks will be logged. Note that only application
     * call stacks are counted.
     */
    public $traceLevel = 0;
    /**
     * @var Dispatcher the message dispatcher
     */
    public $dispatcher;

    public function init()
    {
        parent::init();
    }

    public function log($message, $level, $category = 'application')
    {
        $time = microtime(true);
        $traces = [];
//        if ($this->traceLevel > 0) {
//            $count = 0;
//            $ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//            array_pop($ts); // remove the last trace since it would be the entry script, not very useful
//            foreach ($ts as $trace) {
//                if (isset($trace['file'], $trace['line']) && strpos($trace['file'], YII2_PATH) !== 0) {
//                    unset($trace['object'], $trace['args']);
//                    $traces[] = $trace;
//                    if (++$count >= $this->traceLevel) {
//                        break;
//                    }
//                }
//            }
//        }
        $this->messages[] = [$message, $level, $category, $time, $traces, memory_get_usage()];
        if ($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }

    public function flush()
    {
        $messages = $this->messages;
        // https://github.com/yiisoft/yii2/issues/5619
        // new messages could be logged while the existing ones are being handled by targets
        $this->messages = [];
        $this->dispatcher->dispatch($messages, $final);
    }
}