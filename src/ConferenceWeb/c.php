<?php
declare(strict_types=1);

use Ramsey\Uuid\Uuid;
use Shared\RabbitMQ\Exchange;

function order()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $command = $_POST;
        $command['order_id'] = Uuid::uuid4();
        $command['_type'] = 'orders_and_registrations.place_order';

        Exchange::publishCommand($command);

        header('Location: /?c=thank_you&orderId=' . $command['order_id']);
        exit;
    }

    $conferences = json_decode(file_get_contents('http://conference_management/?c=list_conferences'), true);

    ?>
    <form action="#" method="post">
        <div>
            <label for="conference_id">Select a conference:</label>
            <select id="conference_id" name="conference_id">
                <?php foreach ($conferences as $conference): ?>
                    <option value="<?php echo $conference['id']; ?>"><?php echo htmlentities($conference['name'],
                            ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="number_of_tickets">Number of tickets:</label> <input id="number_of_tickets"
                                                                             name="number_of_tickets" type="number"/>
        </div>
        <button type="submit">Place order</button>
    </form>
    <?php

    exit;
}

function thank_you()
{
    ?>
    <p>Thank you for ordering your ticket(s).</p>
    <?php
}
