<?php

require __DIR__ . '/src/common.php';

use PhpAmqpLib\Message\AMQPMessage;

$consumeCallback = function (AMQPMessage $msg) {
    $delivery_info = $msg->delivery_info;

    $props = $msg->get_properties();
    if (isset($props['application_headers'])) {
        $nativeData = $props['application_headers']->getNativeData();

        $xDeath = current($nativeData)[0]['count'];

        if ($xDeath >= 10) {
            file_put_contents('failed_payload.log', $msg->body . PHP_EOL, FILE_APPEND);
            $delivery_info['channel']->basic_ack($delivery_info['delivery_tag']);

            return;
        }
    }

    $delivery_info['channel']->basic_nack($delivery_info['delivery_tag'], false, false);
};

$channel->basic_consume(
    $normalQueue,
    'andreyors/rmq-dlx-consumer',
    false,
    false,
    false,
    false,
    $consumeCallback
);

while (count($channel->callbacks) > 0) {
    $channel->wait();
}
