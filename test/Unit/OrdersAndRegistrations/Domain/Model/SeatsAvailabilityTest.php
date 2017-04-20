<?php
declare(strict_types=1);

namespace Test\Unit\OrdersAndRegistrations\Domain\Model;

use OrdersAndRegistrations\ConferenceId;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationCancelled;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationCommitted;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationId;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationRejected;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\SeatsAvailability;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\SeatsAvailabilityCreated;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationAccepted;
use Ramsey\Uuid\Uuid;

class SeatsAvailabilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_for_a_given_conference_id_and_quantity_of_seats()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $quantity = 10;

        $seatsAvailability = SeatsAvailability::create($conferenceId, $quantity);

        $this->assertEquals(
            [
                new SeatsAvailabilityCreated($conferenceId, $quantity)
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }

    /**
     * @test
     */
    public function a_reservation_can_be_made_if_the_number_of_seats_doesnt_exceed_the_available_number()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $quantity = 10;
        $seatsAvailability = SeatsAvailability::create($conferenceId, $quantity);
        $reservationId = ReservationId::fromString((string)Uuid::uuid4());
        // clear recorded events
        $seatsAvailability->popRecordedEvents();

        $seatsAvailability->makeReservation($reservationId, 10);

        $this->assertEquals(
            [
                new ReservationAccepted($conferenceId, $reservationId, $quantity)
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }

    /**
     *
     * @test
     */
    public function a_reservation_cant_be_made_if_the_number_of_seats_exceeds_the_available_number()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $quantity = 1;
        $seatsAvailability = SeatsAvailability::create($conferenceId, $quantity);
        $reservationId = ReservationId::fromString((string)Uuid::uuid4());
        // clear recorded events
        $seatsAvailability->popRecordedEvents();

        $seatsAvailability->makeReservation($reservationId, 2);

        $this->assertEquals(
            [
                new ReservationRejected($conferenceId, $reservationId, 2)
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }

    /**
     *
     * @test
     */
    public function a_reservation_cant_be_made_if_the_number_of_seats_exceeds_the_accumulated_available_number()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $seatsAvailability = SeatsAvailability::create($conferenceId, 3);
        // clear recorded events
        $seatsAvailability->popRecordedEvents();

        // first reservation will be accepted
        $reservation1Id = ReservationId::fromString((string)Uuid::uuid4());
        $seatsAvailability->makeReservation($reservation1Id, 2);

        // second reservation will be rejected
        $reservation2Id = ReservationId::fromString((string)Uuid::uuid4());
        $seatsAvailability->makeReservation($reservation2Id, 2);

        $this->assertEquals(
            [
                new ReservationAccepted($conferenceId, $reservation1Id, 2),
                new ReservationRejected($conferenceId, $reservation2Id, 2)
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }

    /**
     * @test
     */
    public function cancelling_a_reservation_frees_up_the_number_of_seats_available()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $seatsAvailability = SeatsAvailability::create($conferenceId, 2);
        // clear recorded events
        $seatsAvailability->popRecordedEvents();

        // first reservation will be accepted
        $reservation1Id = ReservationId::fromString((string)Uuid::uuid4());
        $seatsAvailability->makeReservation($reservation1Id, 2);

        $seatsAvailability->cancelReservation($reservation1Id);

        // second reservation will be accepted, since the first one has been cancelled
        $reservation2Id = ReservationId::fromString((string)Uuid::uuid4());
        $seatsAvailability->makeReservation($reservation2Id, 2);

        $this->assertEquals(
            [
                new ReservationAccepted($conferenceId, $reservation1Id, 2),
                new ReservationCancelled($conferenceId, $reservation1Id, 2),
                new ReservationAccepted($conferenceId, $reservation2Id, 2)
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }

    /**
     * @test
     */
    public function you_can_commit_a_reservation()
    {
        $conferenceId = ConferenceId::fromString((string)Uuid::uuid4());
        $seatsAvailability = SeatsAvailability::create($conferenceId, 2);
        // clear recorded events
        $seatsAvailability->popRecordedEvents();

        // first reservation will be accepted
        $reservationId = ReservationId::fromString((string)Uuid::uuid4());
        $seatsAvailability->makeReservation($reservationId, 2);

        $seatsAvailability->commitReservation($reservationId);

        $this->assertEquals(
            [
                new ReservationAccepted($conferenceId, $reservationId, 2),
                new ReservationCommitted($conferenceId, $reservationId, 2),
            ],
            $seatsAvailability->popRecordedEvents()
        );
    }
}
