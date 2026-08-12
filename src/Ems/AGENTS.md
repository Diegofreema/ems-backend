# EMS domain services

## Overview

This area holds reusable EMS business rules. It owns tenant isolation, viewer scope, policies, money, grading, academic calculations, fees, imports, reports, analytics, communication, storage, and wire serialization.

## Key files

| File | Owns |
|---|---|
| `Tenant.php` | Adds `school_id` scope to EMS table access |
| `Scope.php` | Limits students and classes to the current viewer |
| `Policy.php` | Role and state transition rules |
| `Invitations.php` | Hashed expiring invitation codes and email delivery |
| `Money.php` | Integer money arithmetic and validation |
| `FinanceChain.php` | Signed ledger link traversal, fork detection, and chain-tip resolution |
| `FinanceKeys.php` | Active and retained finance HMAC keys loaded from environment configuration |
| `FinanceSecurity.php` | Finance idempotency, evidence, approval, posting, receipts, and integrity locks |
| `ClamAv.php` | Fail-closed private evidence scanning through the ClamAV daemon |
| `Academics.php` | CA derivation, grading sheets, report cards, and transcripts |
| `Fees.php` | Invoice pricing, awards, balances, receipts, and reconciliation |
| `Serializer/` | Stable frontend wire shapes |

## Conventions

* Accept a CakePHP table locator and explicit school identifier in stateful services.
* Read and write EMS data through `Tenant`, not a bare table query.
* Keep calculations deterministic and separate from controller transport code.
* Preserve the exact response semantics documented in `document.md`, including sort order and omitted values.
* Use integer minor units for money. Avoid floating point arithmetic.
* Keep serializers total so saved entities and queried entities produce the same wire shape.
* Treat posted payments, receipts, ledger events, evidence, decisions, and finance audits as append-only records. Corrections use compensating events.
* Load the active and retained finance HMAC keys from `EMS_FINANCE_AUDIT_HMAC_ACTIVE_KEY_ID` and `EMS_FINANCE_AUDIT_HMAC_KEYS_JSON`; never store those secrets in MySQL.
* Evidence remains quarantined and cannot be approved unless ClamAV returns a clean result.
* Clear a finance integrity lock only after a successful full verification by an active administrator in the same school.

## Gotchas

* EMS primary keys are stored as `CHAR(36)`. `EmsTable` changes their reflected type to `uuid` so CakePHP generates identifiers.
* Subject names on the wire map to normalized `subject_id` values through `SubjectCatalog`.
* Some implementation comments mention the former frontend mock because byte level parity was an explicit migration requirement.

## Related specs

* [`document.md`](../../document.md)

_Drafted by /audit from the repo, worth a quick human pass. Edit freely: once a line stops matching this draft, later runs treat it as curated and will flag rather than overwrite it._
