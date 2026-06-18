# hval/nexi-php

Unofficial PHP library for the [Nexi XPay](https://developer.nexigroup.com/) payment gateway: Hosted Payment Page (HPP), Pay-by-Link, recurring contracts, operations and webhooks.

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
    'Mario Rossi',
    'mario@example.com',
    $billing,
    null,   // shippingAddress
    '39',   // mobilePhoneCountryCode
    '3331234567'
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
```

### 5. List orders and operations

```php
// List orders — all parameters are optional
$orders = $nexi->orders()->findAll(
    '2024-01-01T00:00:00.000Z',  // fromTime (ISO 8601)
    '2024-01-31T23:59:59.000Z',  // toTime   (ISO 8601, max 30-day range)
    50,                          // maxRecords (default 20, max 500)
    'promo2024'                  // customField
);

foreach ($orders as $order) {       // array<int, OrderSummary>
    $order->getOrderId();           // ?string
    $order->getAmount();            // ?string
    $order->getLastOperationType(); // ?string
}

// List operations — filter by channel or type
$operations = $nexi->operations()->findAll(
    fromTime: null,
    toTime: null,
    maxRecords: null,
    channel: 'ECOMMERCE',           // ECOMMERCE, POS, BACKOFFICE
    operationType: 'AUTHORIZATION'
);

// Retrieve a single operation
$operation = $nexi->operations()->find('operation-id');
$operation->getOperationResult(); // ?string
$operation->getWarnings();        // array
```

### 6. Payment methods

```php
$methods = $nexi->paymentMethods()->listAll();

foreach ($methods as $method) {       // array<int, PaymentMethod>
    $method->getMethodType();         // 'CARD' or 'APM'
    $method->getCircuit();            // 'VISA', 'MC', 'PAYPAL', ...
    $method->getImageLink();          // SVG logo URL
    $method->isRecurringSupported();  // ?bool
    $method->isOneClickSupported();   // ?bool
}
```

### 7. Pay-by-Link

```php
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;

$order   = new Order('ORDER-001', '1000', 'EUR');
$session = new PaymentSession(
    PaymentSession::ACTION_PAY,
    '1000',
    'ita',
    'https://yoursite.com/payment/result',
    'https://yoursite.com/payment/cancel'
);

// expirationDate is required (max 90 days, YYYY-MM-DD)
$response = $nexi->payByLink()->create($order, $session, '2024-12-31');

$link = $response->getPaymentLink();
$link->getLinkId();        // ?string — use to cancel the link
$link->getLink();          // ?string — send this URL to the customer
$link->getSecurityToken(); // ?string — save in DB for webhook verification

// Cancel an active link
$nexi->payByLink()->cancel($link->getLinkId());
```

### 8. Recurring contracts

```php
// Retrieve all contracts for a customer
$response = $nexi->contracts()->findByCustomer('customer-id');

$response->getCustomerId(); // ?string

foreach ($response->getContracts() as $contract) { // array<int, ContractSummary>
    $contract->getContractId();            // ?string
    $contract->getContractType();          // MIT_UNSCHEDULED, MIT_SCHEDULED, CIT
    $contract->getPaymentCircuit();        // ?string
    $contract->getPaymentInstrumentInfo(); // ?string
}

// Deactivate a contract
$nexi->contracts()->deactivate('contract-id');
```

### 9. Recurring payments

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

For the full list of available getters on each response model, see [docs/response-objects.md](docs/response-objects.md).

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
