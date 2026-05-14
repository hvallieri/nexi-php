# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.2] - 2026-05-14

### Fixed

- `OperationService`: added mandatory `Idempotency-Key` header (UUID v4) to `POST /operations/{id}/refunds` and `POST /operations/{id}/captures`, required by the Nexi API (error PS0074); an optional `$idempotencyKey` parameter allows callers to supply their own key for safe retries

## [1.1.1] - 2026-05-05

### Fixed

- `OrderResponse`: corrected parsing of the `GET /orders/{orderId}` response — `authorizedAmount`, `capturedAmount`, `lastOperationType` and `lastOperationTime` are direct properties of `orderStatus`, not nested under `orderStatus.order`; `operations` is a top-level key of the response, not nested inside `orderStatus`

## [1.1.0] - 2026-05-05

### Added

- `CustomerInfo`: added `mobilePhoneCountryCode`, `mobilePhone`, `homePhone` and `workPhone` fields
- `Order`: added `termsAndConditionsIds` (list of accepted T&C UUIDs) and `transactionSummary`
- `RefundRequest`: `amount` and `currency` are now optional — omitting both triggers a full refund on the Nexi side
- `CaptureRequest`: `amount` and `currency` are now optional — omitting both triggers a full capture on the Nexi side

## [1.0.3] - 2026-05-14

### Fixed

- `OperationService`: added mandatory `Idempotency-Key` header (UUID v4) to `POST /operations/{id}/refunds` and `POST /operations/{id}/captures`, required by the Nexi API (error PS0074); an optional `$idempotencyKey` parameter allows callers to supply their own key for safe retries

## [1.0.2] - 2026-05-05

### Fixed

- `OrderResponse`: corrected parsing of the `GET /orders/{orderId}` response — `authorizedAmount`, `capturedAmount`, `lastOperationType` and `lastOperationTime` are direct properties of `orderStatus`, not nested under `orderStatus.order`; `operations` is a top-level key of the response, not nested inside `orderStatus`

## [1.0.1] - 2026-05-05

### Fixed

- `OrderResponse`: corrected parsing of the `GET /orders/{orderId}` response — fields are nested under `orderStatus.order`, not at root level; `getStatus()` has been replaced by `getLastOperationResult()` (the status value comes from `operations[0].operationResult`, not from a dedicated field); constants renamed from `ORDER_STATUS_*` to `OPERATION_RESULT_*` and extended with all values documented by the API (`DENIED_BY_RISK`, `THREEDS_VALIDATED`, `THREEDS_FAILED`, `VOIDED`, `REFUNDED`, `FAILED`); added `getAuthorizedAmount()`, `getCapturedAmount()`, `getLastOperationType()`, `getLastOperationTime()`, `getOperations()`; `isAuthorized()` and `isExecuted()` now return correct results
- `OperationResponse`: removed `getOperationType()`, `getOperationResult()` and `isSuccessful()` — these fields are not present in the API response for refund, capture and cancel operations; added `getOperationTime()`; `RESULT_*` constants removed; `getOperationId()` return type corrected to `?string`
- `WebhookNotification`: corrected parsing — `orderId`, `operationId`, `operationType` and `operationResult` are nested under the `operation` key, not at root level; added `getEventId()`, `getEventTime()`, `getChannel()`, `getOperationTime()`, `getPaymentMethod()`, `getPaymentCircuit()`, `getOperationAmount()`, `getOperationCurrency()`, `isAuthorized()`, `isExecuted()`; all getter return types corrected to `?string`
- `HppResponse`: `getHostedPage()` and `getSecurityToken()` return type corrected to `?string`
- README: fixed integer literals in `RefundRequest` and `CaptureRequest` examples (`1000` → `'1000'`)

## [1.0.0] - 2026-04-26

### Added

- `NexiClient` entry point with `orders()`, `operations()` and `webhooks()` services
- `OrderService`: create HPP orders (`POST /orders/hpp`) and retrieve order status (`GET /orders/{orderId}`)
- `OperationService`: refund, capture and cancel operations
- `WebhookHandler`: webhook signature verification via `hash_equals` on `securityToken`
- Full PSR-18 / PSR-17 / PSR-7 compliance — bring your own HTTP client
- Exception hierarchy: `NexiException`, `AuthenticationException`, `InvalidRequestException`, `ApiException`, `WebhookSignatureException`
- Sandbox and production environment support via `NexiClient::ENV_SANDBOX` and `NexiClient::ENV_PRODUCTION`
