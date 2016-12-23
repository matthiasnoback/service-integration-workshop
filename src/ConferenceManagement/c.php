<?php
declare(strict_types=1);

use ConferenceManagement\Conference;

function list_conferences()
{
    header('Content-Type: application/json');
    echo json_encode([
            new Conference(
                'fd29186c-b1e1-46f5-97ad-17d1bbad7c9d',
                'DDD Europe',
                new \DateTime('2017-02-02'),
                new \DateTime('2017-02-03'),
                'Amsterdam'
            ),
            new Conference(
                '99d8bbd5-1d97-4bf4-85c3-246a143c2521',
                'DDDx',
                new \DateTime('2017-04-27'),
                new \DateTime('2017-04-28'),
                'London'
            ),
            new Conference(
                'f7e14c73-a6dc-4f59-ac8f-7ed875052056',
                'Explore DDD',
                new \DateTime('2017-09-21'),
                new \DateTime('2017-09-22'),
                'Denver'
            ),
        ]
    );
    exit;
}
