<?php
use toom1996\http\ErrorHandler;

/**
 * @var $exception Exception|\toom1996\http\HttpException
 * @var $handler ErrorHandler
 */

$this->title = 'Goblin framework!';
?>
<link rel="stylesheet" type="text/css" href="/css/style.css">
<style>
    h1 {
        font-family: 'Luckiest Guy',sans-serif;
        color: #f4e0f1;
        font-size: 55px;
        bottom: 0px;
        margin-bottom: 0px;
        margin-top: 0px;
    }
    h2 {
        font-size: 20px;
        line-height: 1.25;
        color: #ff6b81;
        font-weight: bold;
    }

    .header {
        padding: 40px 50px 30px 50px;
        background: #281e36;
        border-bottom: 1px solid #44395d;
        text-align: center;
    }
</style>
<div class="container">
    <div class="header">
        <h1>
            <?php if ($exception instanceof \toom1996\http\HttpException): ?>
                <?= '<span>' . $handler->createHttpStatusLink($exception->statusCode, $handler->htmlEncode($exception->getName())) . '</span>' . ' &ndash; ' . $handler->addTypeLinks(get_class($exception));?>
            <?php else: ?>
                <?= $handler->getExceptionName($exception) !== null
                    ? '<span>' . $handler->htmlEncode($name) . '</span>' . ' &ndash; ' . $handler->addTypeLinks(get_class($exception))
                    : '<span>' . $handler->htmlEncode(get_class($exception)) . '</span>';
                ; ?>
            <?php endif; ?>
        </h1>
    </div>
</div>