<?php


namespace toom1996\log;


use Swoole\Coroutine;
use Swoole\Coroutine\System;
use toom1996\base\InvalidConfigException;
use toom1996\helpers\FileHelper;
use toom1996\http\Goblin;
use function Swoole\Coroutine\run;

class FileTarget extends Target
{
    /**
     * @var bool whether log files should be rotated when they reach a certain [[maxFileSize|maximum size]].
     * Log rotation is enabled by default. This property allows you to disable it, when you have configured
     * an external tools for log rotation on your server.
     * @since 2.0.3
     */
    public $enableRotation = true;
    /**
     * @var int maximum log file size, in kilo-bytes. Defaults to 10240, meaning 10MB.
     */
    public $maxFileSize = 10240; // in KB
    /**
     * @var int number of log files used for rotation. Defaults to 5.
     */
    public $maxLogFiles = 5;
    /**
     * @var int the permission to be set for newly created log files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;
    /**
     * @var int the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;
    /**
     * @var bool Whether to rotate log files by copy and truncate in contrast to rotation by
     * renaming files. Defaults to `true` to be more compatible with log tailers and is windows
     * systems which do not play well with rename on open files. Rotation by renaming however is
     * a bit faster.
     *
     * The problem with windows systems where the [rename()](https://secure.php.net/manual/en/function.rename.php)
     * function does not work with files that are opened by some process is described in a
     * [comment by Martin Pelletier](https://secure.php.net/manual/en/function.rename.php#102274) in
     * the PHP documentation. By setting rotateByCopy to `true` you can work
     * around this problem.
     */
    public $rotateByCopy = true;

    public function init()
    {
        parent::init();
        if ($this->maxLogFiles < 1) {
            $this->maxLogFiles = 1;
        }
        if ($this->maxFileSize < 1) {
            $this->maxFileSize = 1;
        }
    }
    public function log($message, $level, $category = 'application')
    {
        $time = microtime(true);
        $traces = [];
//        if ($this->traceLevel > 0) {
//            $count = 0;
//            $ts = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//            var_dump($ts);
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
        $this->messages[] = [$message, $level, $category, $time, $traces];
        if ($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }

    public function export()
    {
        $this->logFile = Goblin::getAlias($this->logFile);
        if (strpos($this->logFile, '://') === false || strncmp($this->logFile, 'file://', 7) === 0) {
            $logPath = dirname($this->logFile);
            FileHelper::createDirectory($logPath, $this->dirMode, true);
        }

        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";

        if (($fp = @fopen($this->logFile, 'a+')) === false) {
            throw new InvalidConfigException("Unable to append to log file: {$this->logFile}");
        }

        @flock($fp, LOCK_EX);
        if ($this->enableRotation) {
            // clear stat cache to ensure getting the real current file size and not a cached one
            // this may result in rotating twice when cached file size is used on subsequent calls
            clearstatcache();
        }

        if ($this->enableRotation && @filesize($this->logFile) > $this->maxFileSize * 1024) {
            @flock($fp, LOCK_UN);
            @fclose($fp);
            $this->rotateFiles();
            $writeResult = @file_put_contents($this->logFile, $text, FILE_APPEND | LOCK_EX);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file ({$this->logFile})!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file ({$this->logFile})! Wrote $writeResult out of $textSize bytes.");
            }
        } else {
            $writeResult = @fwrite($fp, $text);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new LogRuntimeException("Unable to export log through file ({$this->logFile})!: {$error['message']}");
            }
            $textSize = strlen($text);
            if ($writeResult < $textSize) {
                throw new LogRuntimeException("Unable to export whole log through file ({$this->logFile})! Wrote $writeResult out of $textSize bytes.");
            }
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        if ($this->fileMode !== null) {
            @chmod($this->logFile, $this->fileMode);
        }
    }


    protected function rotateFiles()
    {
        $file = $this->logFile;
        for ($i = $this->maxLogFiles; $i >= 0; --$i) {
            // $i == 0 is the original log file
            $rotateFile = $file . ($i === 0 ? '' : '.' . $i);
            if (is_file($rotateFile)) {
                // suppress errors because it's possible multiple processes enter into this section
                if ($i === $this->maxLogFiles) {
                    @unlink($rotateFile);
                } else {
                    if ($this->rotateByCopy) {
                        @copy($rotateFile, $file . '.' . ($i + 1));
                        if ($fp = @fopen($rotateFile, 'a')) {
                            @ftruncate($fp, 0);
                            @fclose($fp);
                        }
                        if ($this->fileMode !== null) {
                            @chmod($file . '.' . ($i + 1), $this->fileMode);
                        }
                    } else {
                        @rename($rotateFile, $file . '.' . ($i + 1));
                    }
                }
            }
        }
    }
}