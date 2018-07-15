<?php

require __DIR__ . '/src/common.php';

use PhpAmqpLib\Message\AMQPMessage;

$msg = new AMQPMessage(
    date('Y-m-d H:i:s'),
    [
        'delivery_mode' => 2,
    ]
);

$channel->basic_publish($msg, $normalExchange);
