<?php

namespace Shared\Persistence;

use Ramsey\Uuid\UuidInterface;
use function Shared\CommandLine\line;
use function Shared\CommandLine\make_cyan;
use function Shared\CommandLine\stdout;

class DB
{
    public static function persist(CanBePersisted $object)
    {
        $allData = self::loadAllData();
        $allData[get_class($object)][$object->id()->toString()] = $object;
        self::saveAllData($allData);

        stdout(line('Persisted object ', make_cyan(get_class($object)), ':', make_cyan($object->id()->toString())));
    }

    public static function retrieve(string $className, UuidInterface $id)
    {
        return self::loadAllData()[$className][$id->toString()];
    }

    private static function loadAllData() : array
    {
        if (!file_exists(self::databaseFilePath())) {
            return [];
        }

        return unserialize(file_get_contents(self::databaseFilePath()));
    }

    private static function saveAllData(array $allData)
    {
        file_put_contents(self::databaseFilePath(), serialize($allData));
    }

    public static function databaseFilePath()
    {
        return realpath(__DIR__ . '/../../../var/db/') . '/' . getenv('SERVICE_NAME');
    }
}
