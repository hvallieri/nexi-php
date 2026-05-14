# hval/nexi-php

PHP library for integrating [Nexi XPay](https://developer.nexigroup.com/) via the Hosted Payment Page (HPP) flow.

## Requirements

- PHP >= 7.2
- Any PSR-18 compatible HTTP client (e.g. `guzzlehttp/guzzle`, `symfony/http-client`)
- A PSR-7 / PSR-17 implementation (e.g. `nyholm/psr7`, `guzzlehttp/psr7`)

## Installation

```bash
composer require hval/nexi-php
```

Install a PSR-18 client if you don't already have one:

```bash
# Guzzle
composer require guzzlehttp/guzzle

# Symfony HttpClient
composer require symfony/http-client nyholm/psr7
```

## Quick Start

### 1. Instantiate the client

**With Guzzle:**

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory as GuzzleFactory;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\NexiClient;

$guzzle  = new GuzzleFactory();
$factory = new HttpFactory($guzzle, $guzzle);

$nexi = new NexiClient('your-api-key', NexiClient::ENV_SANDBOX, new Client(), $factory);
```

**With Symfony HttpClient:**

```php
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\NexiClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

$psr17   = new Psr17Factory();
$factory = new HttpFactory($psr17, $psr17);

$nexi = new NexiClient('your-api-key', NexiClient::ENV_SANDBOX, new Psr18Client(), $factory);
```

### 2. Create an order (HPP flow)

```php
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;

$order   = new Order('ORDER-001', '1000', 'EUR'); // 10.00 EUR
$session = new PaymentSession(
    PaymentSession::ACTION_PAY,
    '1000',
    'ita',
    'https://yoursite.com/payment/result',
    'https://yoursite.com/payment/cancel'
);

$response = $nexi->orders()->createHpp($order, $session);

// Save the securityToken in your DB linked to the order
$_SESSION['nexi_token'] = $response->getSecurityToken();

// Redirect the user
header('Location: ' . $response->getHostedPage());
```

To attach customer details, pass a `CustomerInfo` object:

```php
use Hval\Nexi\Model\Request\Address;
use Hval\Nexi\Model\Request\CustomerInfo;
use Hval\Nexi\Model\Request\Order;

$billing = new Address('Mario Rossi', 'Via Roma 1', 'Milano', '20100', 'ITA');

$customerInfo = new CustomerInfo(
    'Mario Rossi',    // cardHolderName
    'mario@example.com', // cardHolderEmail
    $billing,         // billingAddress
    null,             // shippingAddress
    '39',             // mobilePhoneCountryCode
    '3331234567'      // mobilePhone
);

$order = new Order('ORDER-001', '1000', 'EUR', null, null, null, $customerInfo);
```

### 3. Handle the webhook

```php
use Hval\Nexi\Exception\WebhookSignatureException;

$payload    = file_get_contents('php://input');
$savedToken = '...'; // retrieved from your DB

try {
    $notification = $nexi->webhooks()->handle($payload, $savedToken);

    // Quick check directly on the notification
    if ($notification->isAuthorized()) {
        // Fetch the full order to confirm server-side
        $order = $nexi->orders()->find($notification->getOrderId());

        if ($order->isAuthorized()) {
            // Order confirmed — use $notification->getOperationId() for
            // subsequent refund / capture operations
        }
    }
} catch (WebhookSignatureException $e) {
    http_response_code(400);
    exit;
}
```

### 4. Refund, capture, cancel

Pass the `operationId` from the webhook notification (or from `OrderResponse::getOperations()`):

```php
use Hval\Nexi\Model\Request\CancelRequest;
use Hval\Nexi\Model\Request\CaptureRequest;
use Hval\Nexi\Model\Request\RefundRequest;

$operationId = $notification->getOperationId();

// Partial refund / capture — amount in cents as string, currency required
$result = $nexi->operations()->refund($operationId, new RefundRequest('1000', 'EUR'));
$result = $nexi->operations()->capture($operationId, new CaptureRequest('1000', 'EUR'));

// Full refund / capture — omit amount and currency
$result = $nexi->operations()->refund($operationId, new RefundRequest());
$result = $nexi->operations()->capture($operationId, new CaptureRequest());

$result = $nexi->operations()->cancel($operationId, new CancelRequest());

// Optionally pass your own idempotency key to make retries safe.
// If omitted, a UUID is generated automatically.
$result = $nexi->operations()->refund($operationId, new RefundRequest('1000', 'EUR'), 'your-uuid-v4');
$result = $nexi->operations()->capture($operationId, new CaptureRequest('1000', 'EUR'), 'your-uuid-v4');

// OperationResponse
$result->getOperationId();   // ?string
$result->getOperationTime(); // ?string
```

### 5. Recurring payments

Pass a `Recurrence` object as the last argument of `PaymentSession` to set up recurring payments:

```php
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Request\Recurrence;

$recurrence = new Recurrence(
    Recurrence::ACTION_CONTRACT_CREATION,
    null,
    Recurrence::CONTRACT_TYPE_MIT_SCHEDULED
);

$session = new PaymentSession(
    PaymentSession::ACTION_PAY,
    '1000',
    'ita',
    'https://yoursite.com/payment/result',
    'https://yoursite.com/payment/cancel',
    null,
    null,
    null,
    null,
    $recurrence
);
```

Available actions: `ACTION_NO_RECURRING`, `ACTION_SUBSEQUENT_PAYMENT`, `ACTION_CONTRACT_CREATION`, `ACTION_CARD_SUBSTITUTION`.

Available contract types: `CONTRACT_TYPE_MIT_UNSCHEDULED`, `CONTRACT_TYPE_MIT_SCHEDULED`, `CONTRACT_TYPE_CIT`.

## Response objects

### `HppResponse` — `orders()->createHpp()`

| Method               | Returns   |
|----------------------|-----------|
| `getHostedPage()`    | `?string` |
| `getSecurityToken()` | `?string` |

### `WebhookNotification` — `webhooks()->handle()`

| Method                   | Returns                                     |
|--------------------------|---------------------------------------------|
| `getEventId()`           | `?string`                                   |
| `getEventTime()`         | `?string`                                   |
| `getSecurityToken()`     | `?string`                                   |
| `getOrderId()`           | `?string`                                   |
| `getOperationId()`       | `?string`                                   |
| `getChannel()`           | `?string`                                   |
| `getOperationType()`     | `?string`                                   |
| `getOperationResult()`   | `?string`                                   |
| `getOperationTime()`     | `?string`                                   |
| `getPaymentMethod()`     | `?string`                                   |
| `getPaymentCircuit()`    | `?string`                                   |
| `getOperationAmount()`   | `?string`                                   |
| `getOperationCurrency()` | `?string`                                   |
| `isAuthorized()`         | `bool` — `operationResult === 'AUTHORIZED'` |
| `isExecuted()`           | `bool` — `operationResult === 'EXECUTED'`   |
| `getRaw()`               | `array`                                     |

### `OrderResponse` — `orders()->find()`

| Method                     | Returns                                        |
|----------------------------|------------------------------------------------|
| `getOrderId()`             | `?string`                                      |
| `getLastOperationResult()` | `?string`                                      |
| `getAuthorizedAmount()`    | `?string`                                      |
| `getCapturedAmount()`      | `?string`                                      |
| `getLastOperationType()`   | `?string`                                      |
| `getLastOperationTime()`   | `?string`                                      |
| `getOperations()`          | `array` — raw operation list from the API      |
| `isAuthorized()`           | `bool` — last operation result is `AUTHORIZED` |
| `isExecuted()`             | `bool` — last operation result is `EXECUTED`   |
| `getRaw()`                 | `array`                                        |

Available `operationResult` values as constants on `OrderResponse`:

```
OPERATION_RESULT_PENDING · AUTHORIZED · EXECUTED · DECLINED
OPERATION_RESULT_DENIED_BY_RISK · THREEDS_VALIDATED · THREEDS_FAILED
OPERATION_RESULT_CANCELED · VOIDED · REFUNDED · FAILED
```

### `OperationResponse` — `operations()->refund()` / `capture()` / `cancel()`

| Method               | Returns   |
|----------------------|-----------|
| `getOperationId()`   | `?string` |
| `getOperationTime()` | `?string` |
| `getRaw()`           | `array`   |

## Exceptions

All exceptions extend `NexiException`, which can be used as a catch-all.

| Exception                   | When                      |
|-----------------------------|---------------------------|
| `AuthenticationException`   | 401 — invalid API key     |
| `InvalidRequestException`   | 400 — malformed request   |
| `ApiException`              | other 4xx / 5xx responses |
| `WebhookSignatureException` | security token mismatch   |

## Running Tests

```bash
composer install
./vendor/bin/phpunit
```

## Credits

- [Hermann Vallieri](https://github.com/hvallieri)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
