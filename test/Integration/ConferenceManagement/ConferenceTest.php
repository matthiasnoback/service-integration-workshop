<?php
declare(strict_types=1);

namespace Test\Integration\ConferenceManagement;

use ConferenceManagement\Conference;
use NaiveSerializer\Serializer;

class ConferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_serialized_and_deserialized()
    {
        $id = 'a0d3b607-6d3d-4452-8726-d335990bf8da';
        $start = new \DateTimeImmutable();
        $end = new \DateTimeImmutable('+1 day');
        $name = 'The name';
        $city = 'Zeist';
        $availableTickets = 500;

        $conference = new Conference($id, $name, $start, $end, $city, $availableTickets);

        $serializedData = Serializer::serialize($conference);
        $deserializedConference = Serializer::deserialize(Conference::class, $serializedData);

        $this->assertEquals($conference, $deserializedConference);
    }
}
