<?php
declare(strict_types=1);

namespace Test\Integration\OrdersAndRegistrations;

use NaiveSerializer\Serializer;
use OrdersAndRegistrations\Domain\Model\Order\ConferenceId;
use OrdersAndRegistrations\Domain\Model\Order\OrderId;
use OrdersAndRegistrations\Domain\Model\Order\OrderPlaced;
use Ramsey\Uuid\Uuid;

class OrderPlacedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_serialized()
    {
        $orderId = OrderId::fromString((string)Uuid::uuid4());
        $conferenceId = \OrdersAndRegistrations\Domain\Model\Order\ConferenceId::fromString((string)Uuid::uuid4());
        $numberOfTickets = 2;

        $orderPlaced = new \OrdersAndRegistrations\Domain\Model\Order\OrderPlaced(
            $orderId,
            $conferenceId,
            $numberOfTickets
        );

        $serialized = Serializer::serialize($orderPlaced);
        $deserialized = Serializer::deserialize(OrderPlaced::class, $serialized);

        $this->assertEquals($orderPlaced, $deserialized);
    }
}
