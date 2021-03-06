<?php

$dir = __DIR__ . '/../src/App/Resources/routes/';

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

require $dir . 'auth.php';
require $dir . 'user.php';
require $dir . 'category.php';
require $dir . 'product.php';
require $dir . 'item.php';
require $dir . 'location.php';
require $dir . 'client.php';
require $dir . 'flow.php';

