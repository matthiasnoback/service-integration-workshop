<?php
declare(strict_types=1);

namespace Test\Unit\OrdersAndRegistrations;

use OrdersAndRegistrations\ConferenceId;
use OrdersAndRegistrations\Order;
use OrdersAndRegistrations\OrderId;
use OrdersAndRegistrations\OrderPlaced;
use Ramsey\Uuid\Uuid;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function an_order_can_be_placed()
    {
        $this->markTestIncomplete('First, make Order an event-sourced aggregate');

        $orderId = OrderId::fromString((string)Uuid::uuid4());
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $numberOfTickets = 2;

        $order = Order::place($orderId, $conferenceId, $numberOfTickets);

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
