<?php

namespace Test\Integration\Shared\Persistence;

use Ramsey\Uuid\UuidInterface;
use Shared\Persistence\CanBePersisted;

final class PersistableDummy implements CanBePersisted
{
    private $id;
    private $secretValue;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->secretValue = uniqid();
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }
}
