<?php
declare(strict_types=1);

namespace ConferenceManagement;

use Common\Persistence\Database;
use NaiveSerializer\Serializer;
use Ramsey\Uuid\Uuid;
use Shared\RabbitMQ\Exchange;

final class Application
{
    public function createConferenceController()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conference = new Conference(
                (string)Uuid::uuid4(),
                $_POST['name'],
                new \DateTimeImmutable($_POST['start']),
                new \DateTimeImmutable($_POST['end']),
                $_POST['city'],
                (int)$_POST['availableTickets']
            );
            Database::persist($conference);

            Exchange::publish('conference_management.conference_created', $conference);

            header('Location: /listConferences');
            exit;
        }

        ?>
        <h1>Craete a new conference</h1>
        <form action="/createConference" method="post">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name"/><br>
            <label for="start">Start:</label>
            <input type="text" name="start" id="start"/><br>
            <label for="end">End:</label>
            <input type="text" name="end" id="end"/><br>
            <label for="city">City:</label>
            <input type="text" name="city" id="city"/><br>
            <label for="availableTickets">Available tickets:</label>
            <input type="text" name="availableTickets" id="availableTickets"/><br>
            <button type="submit">Create</button>
        </form>
        <?php

        exit;
    }

    public function listConferencesController()
    {
        header('Content-Type: application/json', true, 200);

        $conferences = Database::retrieveAll(Conference::class);

        if (empty($conferences)) {
            $conferences = [
                new Conference(
                    'fd29186c-b1e1-46f5-97ad-17d1bbad7c9d',
                    'DDD Europe',
                    new \DateTimeImmutable('2017-02-02'),
                    new \DateTimeImmutable('2017-02-03'),
                    'Amsterdam',
                    500
                ),
                new Conference(
                    '99d8bbd5-1d97-4bf4-85c3-246a143c2521',
                    'DDDx',
                    new \DateTimeImmutable('2017-04-27'),
                    new \DateTimeImmutable('2017-04-28'),
                    'London',
                    200
                ),
                new Conference(
                    'f7e14c73-a6dc-4f59-ac8f-7ed875052056',
                    'Explore DDD',
                    new \DateTimeImmutable('2017-09-21'),
                    new \DateTimeImmutable('2017-09-22'),
                    'Denver',
                    600
                ),
            ];
        }

        echo Serializer::serialize($conferences);
        exit;
    }
}
