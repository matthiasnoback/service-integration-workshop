<?php
declare(strict_types=1);

namespace Test\Integration\ConferenceManagement;

use ConferenceManagement\Conference;
use NaiveSerializer\Serializer;
use Ramsey\Uuid\Uuid;

class ConferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_serialized_and_deserialized()
    {
        $id = (string)Uuid::uuid4();
        $start = new \DateTimeImmutable();
        $end = new \DateTimeImmutable('+1 day');
        $name = 'The name';
        $city = 'Zeist';

        $conference = new Conference($id, $name, $start, $end, $city);

        $serializedData = Serializer::serialize($conference);
        $deserializedConference = Serializer::deserialize(Conference::class, $serializedData);

        $this->assertEquals($conference, $deserializedConference);
    }
}
