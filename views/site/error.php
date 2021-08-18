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
        color: #f4e0f1;
        bottom: 0;
        margin-bottom: 0;
        margin-top: 0;
    }

    h1 a {
        color: rgb(200,59,80);
    }
    h2 {
        font-size: 20px;
        line-height: 1.25;
        color: #ff6b81;
        font-weight: bold;
    }

    .c-header-box {
        background: #281e36;
        border-bottom: 1px solid #44395d;
        text-align: center;
        word-wrap:break-word;
    }
</style>
<div class="container">
    <div class="c-header-box">
        <h1>
            <?php if ($exception instanceof \toom1996\http\HttpException): ?>
                <?= $handler->createHttpStatusLink($exception->getCode(), $handler->htmlEncode($exception->getName())) . ' &ndash; ' . get_class($exception);?>
            <?php else: ?>
                <?= $handler->getExceptionName($exception) !== null
                    ? '<span>' . $handler->htmlEncode($name) . '</span>' . ' &ndash; ' . $handler->addTypeLinks(get_class($exception))
                    : '<span>' . $handler->htmlEncode(get_class($exception)) . '</span>';
                ; ?>
            <?php endif; ?>
        </h1>
        <h2><?= nl2br($handler->htmlEncode($exception->getMessage())) ?></h2>
    </div>
</div>