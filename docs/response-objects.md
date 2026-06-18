# Response objects

Reference for all response models returned by [hval/nexi-php](../README.md). Each getter returns `null` when the field is absent from the API response.

## `HppResponse` — `orders()->createHpp()`

| Method               | Returns   |
|----------------------|-----------|
| `getHostedPage()`    | `?string` |
| `getSecurityToken()` | `?string` |

## `OrderSummary` — `orders()->findAll()`

| Method                       | Returns   |
|------------------------------|-----------|
| `getOrderId()`               | `?string` |
| `getAmount()`                | `?string` |
| `getCurrency()`              | `?string` |
| `getCustomerId()`            | `?string` |
| `getDescription()`           | `?string` |
| `getCustomField()`           | `?string` |
| `getAuthorizedAmount()`      | `?string` |
| `getCapturedAmount()`        | `?string` |
| `getLastOperationType()`     | `?string` |
| `getLastOperationTime()`     | `?string` |
| `getTermsAndConditionsIds()` | `?array`  |

## `OrderResponse` — `orders()->find()`

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

Available `operationResult` constants on `OrderResponse`:

```
OPERATION_RESULT_PENDING · AUTHORIZED · EXECUTED · DECLINED
OPERATION_RESULT_DENIED_BY_RISK · THREEDS_VALIDATED · THREEDS_FAILED
OPERATION_RESULT_CANCELED · VOIDED · REFUNDED · FAILED
```

## `OperationDetails` — `operations()->findAll()` / `find()`

| Method                       | Returns   |
|------------------------------|-----------|
| `getOrderId()`               | `?string` |
| `getOperationId()`           | `?string` |
| `getChannel()`               | `?string` |
| `getChannelDetail()`         | `?string` |
| `getOperationType()`         | `?string` |
| `getOperationResult()`       | `?string` |
| `getOperationTime()`         | `?string` |
| `getPaymentMethod()`         | `?string` |
| `getPaymentCircuit()`        | `?string` |
| `getPaymentInstrumentInfo()` | `?string` |
| `getPaymentEndToEndId()`     | `?string` |
| `getCancelledOperationId()`  | `?string` |
| `getOperationAmount()`       | `?string` |
| `getOperationCurrency()`     | `?string` |
| `getPaymentLinkId()`         | `?string` |
| `getTerminalId()`            | `?string` |
| `getWarnings()`              | `array`   |

Available constants on `OperationDetails`:

```
CHANNEL_ECOMMERCE · CHANNEL_POS · CHANNEL_BACKOFFICE
OPERATION_TYPE_AUTHORIZATION · CAPTURE · VOID · REFUND · CANCEL
```

## `OperationResponse` — `operations()->refund()` / `capture()` / `cancel()`

| Method               | Returns   |
|----------------------|-----------|
| `getOperationId()`   | `?string` |
| `getOperationTime()` | `?string` |
| `getRaw()`           | `array`   |

## `PaymentMethod` — `paymentMethods()->listAll()`

| Method                   | Returns                     |
|--------------------------|-----------------------------|
| `getMethodType()`        | `?string` — `CARD` or `APM` |
| `getCircuit()`           | `?string`                   |
| `getImageLink()`         | `?string`                   |
| `isRecurringSupported()` | `?bool`                     |
| `isOneClickSupported()`  | `?bool`                     |

## `PayByLinkResponse` / `PaymentLink` — `payByLink()->create()`

`PayByLinkResponse::getPaymentLink()` returns a `PaymentLink` object:

| Method                   | Returns                                                |
|--------------------------|--------------------------------------------------------|
| `getLinkId()`            | `?string`                                              |
| `getAmount()`            | `?string`                                              |
| `getExpirationDate()`    | `?string`                                              |
| `getLink()`              | `?string`                                              |
| `getPaidByOperationId()` | `?string`                                              |
| `getStatus()`            | `?string` — `ACTIVE`, `DELETED`, `EXPIRED`, `INACTIVE` |
| `getSecurityToken()`     | `?string`                                              |

## `ContractsByCustomerResponse` / `ContractSummary` — `contracts()->findByCustomer()`

`ContractsByCustomerResponse::getContracts()` returns an array of `ContractSummary` objects:

| Method                       | Returns                                               |
|------------------------------|-------------------------------------------------------|
| `getContractId()`            | `?string`                                             |
| `getContractType()`          | `?string` — `MIT_UNSCHEDULED`, `MIT_SCHEDULED`, `CIT` |
| `getContractExpiryDate()`    | `?string`                                             |
| `getContractFrequency()`     | `?string`                                             |
| `getPaymentMethod()`         | `?string`                                             |
| `getPaymentCircuit()`        | `?string`                                             |
| `getPaymentInstrumentInfo()` | `?string`                                             |

## `WebhookNotification` — `webhooks()->handle()`

| Method                           | Returns                                     |
|----------------------------------|---------------------------------------------|
| `getEventId()`                   | `?string`                                   |
| `getEventTime()`                 | `?string`                                   |
| `getEventType()`                 | `?string`                                   |
| `getSecurityToken()`             | `?string`                                   |
| `getPaymentId()`                 | `?string`                                   |
| `getResult()`                    | `?string`                                   |
| `getRootPaymentMethod()`         | `?string`                                   |
| `getRootPaymentInstrumentInfo()` | `?string`                                   |
| `getOrderAmount()`               | `?string`                                   |
| `getCurrency()`                  | `?string`                                   |
| `getCustomerId()`                | `?string`                                   |
| `getDescription()`               | `?string`                                   |
| `getCustomField()`               | `?string`                                   |
| `getOrderTime()`                 | `?string`                                   |
| `getErrorCode()`                 | `?string`                                   |
| `getErrorMessage()`              | `?string`                                   |
| `getAdditionalData()`            | `?WebhookAdditionalData`                    |
| `getOrderId()`                   | `?string` — from `operation`                |
| `getOperationId()`               | `?string` — from `operation`                |
| `getChannel()`                   | `?string` — from `operation`                |
| `getOperationType()`             | `?string` — from `operation`                |
| `getOperationResult()`           | `?string` — from `operation`                |
| `getOperationTime()`             | `?string` — from `operation`                |
| `getPaymentMethod()`             | `?string` — from `operation`                |
| `getPaymentCircuit()`            | `?string` — from `operation`                |
| `getOperationAmount()`           | `?string` — from `operation`                |
| `getOperationCurrency()`         | `?string` — from `operation`                |
| `getPaymentInstrumentInfo()`     | `?string` — from `operation`                |
| `getPaymentEndToEndId()`         | `?string` — from `operation`                |
| `getCancelledOperationId()`      | `?string` — from `operation`                |
| `getPaymentLinkId()`             | `?string` — from `operation`                |
| `getTerminalId()`                | `?string` — from `operation`                |
| `getWarnings()`                  | `array` — from `operation`                  |
| `isAuthorized()`                 | `bool` — `operationResult === 'AUTHORIZED'` |
| `isExecuted()`                   | `bool` — `operationResult === 'EXECUTED'`   |
| `getRaw()`                       | `array`                                     |
