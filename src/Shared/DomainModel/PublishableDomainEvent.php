<?php

namespace Shared\DomainModel;

interface PublishableDomainEvent
{
    public function eventData() : array;
}
