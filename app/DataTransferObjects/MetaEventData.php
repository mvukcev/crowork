<?php

namespace App\DataTransferObjects;

class MetaEventData
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $eventId,
        public readonly array $userData,
        public readonly array $customData = [],
        public readonly ?string $eventSourceUrl = null,
        public readonly string $actionSource = 'website',
        public readonly ?string $clientUserAgent = null,
        public readonly ?string $clientIpAddress = null,
    ) {}
}
