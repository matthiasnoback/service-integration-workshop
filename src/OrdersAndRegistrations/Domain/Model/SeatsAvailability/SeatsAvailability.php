<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\SeatsAvailability;

use Common\EventSourcing\Aggregate\EventSourcedAggregate;
use Common\EventSourcing\Aggregate\EventSourcedAggregateCapabilities;
use OrdersAndRegistrations\Domain\Model\Order\ConferenceId;

final class SeatsAvailability implements EventSourcedAggregate
{
    use EventSourcedAggregateCapabilities;

    /**
     * @var array<string,int>
     */
    private $reservations = [];

    /**
     * @var int
     */
    private $availableSeats;

    /**
     * @var ConferenceId
     */
    private $conferenceId;

    public function id(): string
    {
        return (string)$this->conferenceId;
    }

    public static function create(ConferenceId $conferenceId, int $quantity)
    {
        $seatsAvailability = new static();
        $seatsAvailability->recordThat(new SeatsAvailabilityCreated($conferenceId, $quantity));

        return $seatsAvailability;
    }

    private function whenSeatsAvailabilityCreated(SeatsAvailabilityCreated $event): void
    {
        $this->conferenceId = $event->conferenceId();
        $this->availableSeats = $event->quantity();
    }

    public function makeReservation(ReservationId $reservationId, int $quantity): void
    {
        if ($this->availableSeats >= $quantity) {
            $this->recordThat(new ReservationAccepted($this->conferenceId, $reservationId, $quantity));
        } else {
            $this->recordThat(new ReservationRejected($this->conferenceId, $reservationId, $quantity));
        }
    }

    private function whenReservationAccepted(ReservationAccepted $event): void
    {
        $this->availableSeats -= $event->quantity();

        $this->reservations[(string)$event->reservationId()] = $event->quantity();
    }

    private function whenReservationRejected(ReservationRejected $event): void
    {
    }

    public function cancelReservation(ReservationId $reservationId): void
    {
        if (!isset($this->reservations[(string)$reservationId])) {
            throw new \OutOfBoundsException('Unknown reservation: ' . (string)$reservationId);
        }

        $quantity = $this->reservations[(string)$reservationId];

        $this->recordThat(new ReservationCancelled($this->conferenceId, $reservationId, $quantity));
    }

    private function whenReservationCancelled(ReservationCancelled $event): void
    {
        $this->availableSeats += $event->quantity();

        unset($this->reservations[(string)$event->reservationId()]);
    }

    public function commitReservation($reservationId): void
    {
        if (!isset($this->reservations[(string)$reservationId])) {
            throw new \OutOfBoundsException('Unknown reservation: ' . (string)$reservationId);
        }

        $quantity = $this->reservations[(string)$reservationId];

        $this->recordThat(new ReservationCommitted($this->conferenceId, $reservationId, $quantity));
    }

    private function whenReservationCommitted(ReservationCommitted $event): void
    {
        unset($this->reservations[(string)$event->reservationId()]);
    }
}
