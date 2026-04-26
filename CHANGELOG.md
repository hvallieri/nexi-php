# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-04-26

### Added

- `NexiClient` entry point with `orders()`, `operations()` and `webhooks()` services
- `OrderService`: create HPP orders (`POST /orders/hpp`) and retrieve order status (`GET /orders/{orderId}`)
- `OperationService`: refund, capture and cancel operations
- `WebhookHandler`: webhook signature verification via `hash_equals` on `securityToken`
- Full PSR-18 / PSR-17 / PSR-7 compliance — bring your own HTTP client
- Exception hierarchy: `NexiException`, `AuthenticationException`, `InvalidRequestException`, `ApiException`, `WebhookSignatureException`
- Sandbox and production environment support via `NexiClient::ENV_SANDBOX` and `NexiClient::ENV_PRODUCTION`
