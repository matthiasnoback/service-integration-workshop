# Pick your battles wisely

## Synchronous versus asynchronous communication

`Conference Web` talks to `Orders and Registrations` via messaging (RabbitMQ).

1. Try to accomplish the same thing using HTTP instead (an `orders_and_registrations_web` container is already running, which should help you finish this task).
2. Is there a significant difference? What are the trade-offs for each of the available strategies? THink of several use cases and explain which strategy you would advocate.
3. Try to make the code as flexible as possible: you should be able to switch between handling the `PlaceOrder` command synchronously and asynchronously. Would it be helpful to start using a command bus library (like [SimpleBus](https://github.com/SimpleBus/MessageBus) or [Tactician](https://github.com/thephpleague/tactician))?

## The Order Saga

Take a look at the image `the-place-order-saga.jpg` in this directory. It represents the states of an order and how a *process manager* issues commands and events to finalize an order and make seat reservations. The source is a book called: [CQRS journey](https://msdn.microsoft.com/en-us/library/jj554200.aspx).

1. Continue working on the order [saga](https://github.com/broadway/broadway-saga/tree/master/examples/saga): after placing an order, a seat reservation should be made. When the payment for the order gets accepted, the seat reservation will be committed and the order completed. The `Orders and Registrations` context handles orders and seat reservations, but the yet to be created `Payment` context handles payments. Hence, the last part of this assignment is to:
2. Create the `Payment` context. You don't have to implement payment, but you could redirect the user to a payment page after ordering tickets. When clicking the `Pay` button, you could dispatch the `PaymentReceived` event.

## Querying versus collecting

`Conference Web` makes HTTP requests to `Conference Management` to find out for which conference the user can order tickets. This makes `Conference Web` dependent on `Conference Management`, hence fragile, because an outage of `Conference Management` will take down `Conference Web` with it! You have two main options now (try them both):

1. Try to add a caching layer for conference management calls (possibly using the `symfony/cache` or `doctrine/cache` package). Does this solve the issue with the dependency on `Conference Management`? If you'd like to be really adventurous (you might not finish your work today), then see how [Phystrix](https://github.com/odesk/phystrix) could help in this situation.
2. Another way is to gradually build a data store for `Conference Web` based on domain events dispatched by `Conference Management`. Every time a `Conference` aggregate gets created or modified in the `Conference Management` context, an event will be published, which `Conference Web` processes by updating its list of `Conference` DTOs.

What would the trade-offs for picking either of the above strategies in real-world situations?
