<?php
declare(strict_types=1);

namespace ConferenceManagement;

use NaiveSerializer\Serializer;

final class Application
{
    public function listConferencesController()
    {
        header('Content-Type: application/json');
        echo Serializer::serialize([
                new Conference(
                    'fd29186c-b1e1-46f5-97ad-17d1bbad7c9d',
                    'DDD Europe',
                    new \DateTimeImmutable('2017-02-02'),
                    new \DateTimeImmutable('2017-02-03'),
                    'Amsterdam'
                ),
                new Conference(
                    '99d8bbd5-1d97-4bf4-85c3-246a143c2521',
                    'DDDx',
                    new \DateTimeImmutable('2017-04-27'),
                    new \DateTimeImmutable('2017-04-28'),
                    'London'
                ),
                new Conference(
                    'f7e14c73-a6dc-4f59-ac8f-7ed875052056',
                    'Explore DDD',
                    new \DateTimeImmutable('2017-09-21'),
                    new \DateTimeImmutable('2017-09-22'),
                    'Denver'
                ),
            ]
        );
        exit;
    }
}
