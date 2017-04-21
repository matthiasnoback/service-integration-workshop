<?php
declare(strict_types=1);

namespace Test\Unit\OrdersAndRegistrations;

use OrdersAndRegistrations\Domain\Model\Order\ConferenceId;
use OrdersAndRegistrations\Domain\Model\Order\Order;
use OrdersAndRegistrations\Domain\Model\Order\OrderId;
use OrdersAndRegistrations\Domain\Model\Order\OrderPlaced;
use Ramsey\Uuid\Uuid;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function an_order_can_be_placed()
    {
        $orderId = OrderId::fromString((string)Uuid::uuid4());
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $numberOfTickets = 2;

        $order = \OrdersAndRegistrations\Domain\Model\Order\Order::place($orderId, $conferenceId, $numberOfTickets);

        $this->assertEquals(
            [
                new OrderPlaced(
                    $orderId,
                    $conferenceId,
                    $numberOfTickets
                )
            ],
            $order->popRecordedEvents()
        );
    }
}
