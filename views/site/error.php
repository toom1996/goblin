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

    .call-stack ul li .code .lines-item {
        position: absolute;
        z-index: 200;
        display: block;
        width: 25px;
        text-align: right;
        color: #aaa;
        line-height: 20px;
        font-size: 12px;
        margin-top: 1px;
        font-family: Consolas, monospace;
    }

    .call-stack ul li .error-line, .call-stack ul li .hover-line {
        background-color: #ffebeb;
        position: absolute;
        width: 100%;
        z-index: 100;
        margin-top: 0;
    }

    .call-stack ul li .code-wrap {
        display: none;
        position: relative;
    }
    .call-stack ul li.application .code-wrap {
        display: block;
    }

    .call-stack ul li .code {
        min-width: 860px;
        margin: 15px auto;
        padding: 0 50px;
        position: relative;
    }

    .call-stack ul li .code pre {
        position: relative;
        z-index: 200;
        left: 50px;
        line-height: 20px;
        font-size: 12px;
        font-family: Consolas, monospace;
        display: inline;
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
        <?= $handler->renderPreviousExceptions($exception) ?>
    </div>
    <div class="call-stack">
        <?= $handler->renderCallStack($exception) ?>
    </div>
</div>
<script>
    var callStackItems = document.getElementsByClassName('call-stack-item');
    var refreshCallStackItemCode = function(callStackItem) {
        if (!callStackItem.getElementsByTagName('pre')[0]) {
            console.log('return');
            return;
        }
        var top = callStackItem.getElementsByClassName('code-wrap')[0].offsetTop - window.pageYOffset + 3,
            lines = callStackItem.getElementsByTagName('pre')[0].getClientRects(),
            lineNumbers = callStackItem.getElementsByClassName('lines-item'),
            errorLine = callStackItem.getElementsByClassName('error-line')[0],
            hoverLines = callStackItem.getElementsByClassName('hover-line');

        for (var i = 0, imax = lines.length; i < imax; ++i) {
            if (!lineNumbers[i]) {
                console.log('continue')
                continue;
            }
            console.log(lines[i].top)
            lineNumbers[i].style.top = parseInt(lines[i].top - top) + 'px';
            console.log(parseInt(lines[i].top - top) + 'px')
            hoverLines[i].style.top = parseInt(lines[i].top - top) + 'px';
            hoverLines[i].style.height = parseInt(lines[i].bottom - lines[i].top + 6) + 'px';
            if (parseInt(callStackItem.getAttribute('data-line')) === i) {
                errorLine.style.top = parseInt(lines[i].top - top) + 'px';
                errorLine.style.height = parseInt(lines[i].bottom - lines[i].top + 6) + 'px';
            }
        }
    };
    for (var i = 0, imax = callStackItems.length; i < imax; ++i) {
        refreshCallStackItemCode(callStackItems[i]);
    //
    //     // // toggle code block visibility
    //     // callStackItems[i].getElementsByClassName('element-wrap')[0].addEventListener('click', function(event) {
    //     //     if (event.target.nodeName.toLowerCase() === 'a') {
    //     //         return;
    //     //     }
    //     //
    //     //     var callStackItem = this.parentNode,
    //     //         code = callStackItem.getElementsByClassName('code-wrap')[0];
    //     //
    //     //     if (typeof code !== 'undefined') {
    //     //         code.style.display = window.getComputedStyle(code).display === 'block' ? 'none' : 'block';
    //     //         refreshCallStackItemCode(callStackItem);
    //     //     }
    //     // });
    }
</script>