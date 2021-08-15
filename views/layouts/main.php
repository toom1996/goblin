<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= YiiS::$app->charset ?>" />
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
    <meta name=viewport content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title><?= \toom1996\helpers\HtmlHelper::encode($this->title) ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
<?= $content ?>
</body>
</html>