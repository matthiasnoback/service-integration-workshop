<?php
declare(strict_types=1);

namespace Shared\Persistence;

use Ramsey\Uuid\UuidInterface;

interface CanBePersisted
{
    public function id() : UuidInterface;
}
