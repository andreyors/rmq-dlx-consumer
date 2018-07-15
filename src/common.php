<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Dotenv\Dotenv;

require 'vendor/autoload.php';

(new Dotenv())
    ->load(dirname(__DIR__).'/.env');

$delay = 5; // delay in seconds

$normalQueue = 'queue';
$normalExchange = 'exchange';

$deadLetterQueue = 'queue_deadletter';
$deadLetterExchange = 'exchange_deadletter';

$credentials = \AndreyOrs\Dsn::parse(
    getenv('RABBITMQ_URL')
);

$AMQPConnection = new AMQPStreamConnection(
    $credentials['host'],
    $credentials['port'],
    $credentials['user'],
    $credentials['pass']
);

$channel = $AMQPConnection->channel();

$channel->exchange_declare(
    $deadLetterExchange,
    'direct',
    false,
    true,
    false
);
$channel->queue_declare(
    $deadLetterQueue,
    false,
    true,
    false,
    false,
    false,
    new AMQPTable([
        'x-dead-letter-exchange' => $normalExchange,
        'x-message-ttl' => $delay * 1000,
    ])
);
$channel->queue_bind($deadLetterQueue, $deadLetterExchange);

$channel->exchange_declare(
    $normalExchange,
    'direct',
    false,
    true,
    false
);
$channel->queue_declare(
    $normalQueue,
    false,
    true,
    false,
    false,
    false,
    new AMQPTable([
        'x-dead-letter-exchange' => $deadLetterExchange,
    ])
);

$channel->queue_bind($normalQueue, $normalExchange);

