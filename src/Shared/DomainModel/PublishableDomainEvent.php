<?php
declare(strict_types=1);

namespace Shared\DomainModel;

interface PublishableDomainEvent
{
    public function eventData() : array;
}
