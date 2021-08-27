<?php


namespace toom1996\log;


use toom1996\base\Component;
use toom1996\http\Goblin;

class LogDispatcher extends Component
{
    /**
     * Error message level. An error message is one that indicates the abnormal termination of the
     * application and may require developer's handling.
     */
    const LEVEL_ERROR = 0x01;
    /**
     * Warning message level. A warning message is one that indicates some abnormal happens but
     * the application is able to continue to run. Developers should pay attention to this message.
     */
    const LEVEL_WARNING = 0x02;
    /**
     * Informational message level. An informational message is one that includes certain information
     * for developers to review.
     */
    const LEVEL_INFO = 0x04;
    /**
     * Tracing message level. An tracing message is one that reveals the code execution flow.
     */
    const LEVEL_TRACE = 0x08;
    /**
     * Profiling message level. This indicates the message is for profiling purpose.
     */
    const LEVEL_PROFILE = 0x40;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the
     * beginning of a profiling block.
     */
    const LEVEL_PROFILE_BEGIN = 0x50;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the
     * end of a profiling block.
     */
    const LEVEL_PROFILE_END = 0x60;
    /**
     * @var array|Target[] the log targets. Each array element represents a single [[Target|log target]] instance
     * or the configuration for creating the log target instance.
     */
    public $targets = [];

    /**
     * @var Logger the logger.
     */
    private $_logger;

    /**
     * Default targer class.
     * @var
     */
    public $defaultTargerClass = 'toom1996\log\FileTarget';
    

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        foreach ($this->targets as $name => $target) {
            if (!$target instanceof Target) {
                // Set default target class if target dont have class.
                if (!isset($target['class'])) {
                    $target['class'] = $this->defaultTargerClass;
                }
                $this->targets[$name] = Goblin::createObject($target['class'], $target);
            }
        }
    }

    /**
     *
     * @param $target
     *
     * @return mixed|\toom1996\log\Target
     */
    public function getTarget($target)
    {
        return $this->targets[$target];
    }

    /**
     * @return int how many application call stacks should be logged together with each message.
     * This method returns the value of [[Logger::traceLevel]]. Defaults to 0.
     */
    public function getTraceLevel()
    {
        return $this->getLogger()->traceLevel;
    }

    /**
     * @param int $value how many application call stacks should be logged together with each message.
     * This method will set the value of [[Logger::traceLevel]]. If the value is greater than 0,
     * at most that number of call stacks will be logged. Note that only application call stacks are counted.
     * Defaults to 0.
     */
    public function setTraceLevel($value)
    {
        $this->getLogger()->traceLevel = $value;
    }

    /**
     * @return int how many messages should be logged before they are sent to targets.
     * This method returns the value of [[Logger::flushInterval]].
     */
    public function getFlushInterval()
    {
        return $this->getLogger()->flushInterval;
    }

    /**
     * @param int $value how many messages should be logged before they are sent to targets.
     * This method will set the value of [[Logger::flushInterval]].
     * Defaults to 1000, meaning the [[Logger::flush()]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[Logger::flush()]].
     */
    public function setFlushInterval($value)
    {
        $this->getLogger()->flushInterval = $value;
    }


    /**
     * Generate target error message
     *
     * @param Target $target log target object
     * @param \Throwable|\Exception $throwable catched exception
     * @param string $method full method path
     * @return array generated error message data
     * @since 2.0.32
     */
    protected function generateTargetFailErrorMessage($target, $throwable, $method)
    {
        return [
            'Unable to send log via ' . get_class($target) . ': ' . ErrorHandler::convertExceptionToVerboseString($throwable),
            Logger::LEVEL_WARNING,
            $method,
            microtime(true),
            [],
        ];
    }
}