<?php
declare(strict_types=1);

use Ramsey\Uuid\Uuid;
use Shared\RabbitMQ\Exchange;

function order()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $command = $_POST;
        $command['orderId'] = (string)Uuid::uuid4();

        Exchange::publishCommand('orders_and_registrations.place_order', $command);

        header('Location: /?c=thank_you&orderId=' . $command['orderId']);
        exit;
    }

    $conferences = json_decode(file_get_contents('http://conference_management:8080/?c=list_conferences'), true);

    ?>
    <form action="#" method="post">
        <div>
            <label for="conferenceId">Select a conference:</label>
            <select id="conferenceId" name="conferenceId">
                <?php foreach ($conferences as $conference): ?>
                    <option value="<?php echo $conference['id']; ?>"><?php echo htmlentities($conference['name'],
                            ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="numberOfTickets">Number of tickets:</label> <input id="numberOfTickets"
                                                                           name="numberOfTickets" type="number"/>
        </div>
        <button type="submit">Place order</button>
    </form>
    <?php
}

function thank_you()
{
    ?>
    <p>Thank you for ordering your ticket(s).</p>
    <?php
}
