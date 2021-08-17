<?php
return [
    ['GET', '/user/{id:\d+}.html1', '@controllers/site/error'],
    ['GET', '/user/{id:\d+}.html', 'get_user_handler'],
    ['GET', '/user/test', '@controllers/api/v1/goods/index'],

    '/test' => [
        [['GET', 'POST'], '/gg/{gid:\d+}.jsp', '@controllers/api/v1/goods/index']
    ],
];