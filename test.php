<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(dirname(dirname(__FILE__))) . '/config/config.inc.php';
require_once dirname(dirname(dirname(__FILE__))) . '/init.php';

require_once dirname(__FILE__) . '/classes/loader.php';

$result = QuickAction::createOrder(
    51,
    'Vin.A2.101'
);

echo '<pre>';

var_dump($result);

if ($result instanceof QuickResult) {

    echo PHP_EOL . 'Success: ';
    var_dump($result->isSuccess());

    echo PHP_EOL . 'Message: ';
    var_dump($result->getMessage());

    echo PHP_EOL . 'Data: ';
    var_dump($result->getData());
}

echo '</pre>';