<?php

namespace Shared\Persistence;

use Ramsey\Uuid\UuidInterface;

interface CanBePersisted
{
    public function id() : UuidInterface;
}
