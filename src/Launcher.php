<?php

namespace toom1996;

use toom1996\base\BaseApplication;
use toom1996\base\BaseLauncher;
use toom1996\base\InvalidConfigException;
use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\http\Server;
use toom1996\server\HttpServer;

/**
 * This constant defines the framework installation directory.
 */
defined('EAZY_PATH') or define('EAZY_PATH', __DIR__);

/**
 * Class Application
 *
 * @author: TOOM1996
 */
class Launcher extends BaseLauncher
{
    /**
     * {@inheritDoc}
     */
    public function createServer($config)
    {
        Eazy::setAlias('@eazy', __DIR__);
        return parent::createServer($config);
    }
}