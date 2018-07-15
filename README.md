## RabbitMQ Dead-Letter Explained

### Prerequisites:
- docker
- docker-compose

### Used technologies:
- RabbitMQ 0.9.1 + dead letter exchanges
- PHP 7.2
- PHPAmqpLib 2.6+

### Theory:
RabbitMQ Server has a perfect match for the cross-service communication - dead-letter exchange and queue.

Let's imagine situation when third-party service is not responding in timely manner, it is temporal and we need to repeat processing again.

So we have direct 'exchange' and 'queue' binded to it. Which possible solutions do we have?

- Implement poormans' automatic retry via database with setting status = retry to the entity, which adds some complexity to solution;
- Add new 'exchange_error' and 'queue_error' and manually process it, which is actually one of the RabbitMQ antipatterns called "Filter Queue";
- Use an embedded into RabbitMQ dead-lettering

We will explain one of the most transparent solution:

1. Recreate a 'queue' with x-dead-letter-exchange = 'exchange_deadletter'
2. Add direct 'exchange_deadletter' and 'queue_deadletter' binded to it with
x-dead-letter-exchange = 'exchange', x-message-ttl = 5000 (in milliseconds, 5 secs)
3. In existent consumer processing send basic_nack for the message with requeue = false to drop the message into deadletter queue
4. After expired ttl it will be automatically forwarded to 'queue' again  

```
$ php consumer.php & 
```

```
$ php publisher.php
```


