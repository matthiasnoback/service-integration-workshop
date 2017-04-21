<?php
declare(strict_types=1);

namespace Test\Unit\ConferenceManagement;

use ConferenceManagement\Conference;
use Ramsey\Uuid\Uuid;

class ConferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_a_cruddy_object()
    {
        $id = (string)Uuid::uuid4();
        $start = new \DateTimeImmutable();
        $end = new \DateTimeImmutable('+1 day');
        $name = 'The name';
        $city = 'Zeist';
        $availableTickets = 10;

        $conference = new Conference($id, $name, $start, $end, $city, $availableTickets);

        $this->assertEquals($id, $conference->getId());
        $this->assertEquals($name, $conference->getName());
        $this->assertDateEquals($start, $conference->getStart());
        $this->assertDateEquals($end, $conference->getEnd());
        $this->assertEquals($city, $conference->getCity());
        $this->assertEquals($availableTickets, $conference->getAvailableTickets());
    }

    private function assertDateEquals(\DateTimeImmutable $left, \DateTimeImmutable $right)
    {
        $this->assertEquals(
            $left->format(\DateTime::ATOM),
            $right->format(\DateTime::ATOM)
        );
    }
}
