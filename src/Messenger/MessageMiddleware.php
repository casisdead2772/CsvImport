<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class MessageMiddleware implements MiddlewareInterface {
    public function handle(Envelope $envelope, StackInterface $stack): Envelope {
        if (null === $envelope->last(UniqueIdStamp::class)) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
