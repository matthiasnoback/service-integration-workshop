<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\SeatsAvailability;

use OrdersAndRegistrations\ConferenceId;

final class SeatsAvailabilityCreated
{
    /**
     * @var ConferenceId
     */
    private $conferenceId;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(ConferenceId $conferenceId, int $quantity)
    {
        $this->conferenceId = $conferenceId;
        $this->quantity = $quantity;
    }

    public function conferenceId(): ConferenceId
    {
        return $this->conferenceId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
