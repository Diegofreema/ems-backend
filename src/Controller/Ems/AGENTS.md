# EMS HTTP controllers

## Overview

This area implements the React product API under `/api/ems`. It translates authenticated, tenant scoped requests into domain operations while preserving the response and error shapes in `document.md`.

## Key files

| File | Owns |
|---|---|
| `AppController.php` | Authentication, viewer resolution, tenant checks, CORS, pagination, errors, and shared engines |
| `AuthController.php` | Sign in, validated school registration, expiring invitation redemption, password reset, refresh tokens, and logout |
| `StudentsController.php` | Student records, atomic student and guardian admission, class assignment, attendance, and academics |
| `UsersController.php` | Account invitations, role changes, and tenant checked person links |
| `ExamsController.php` | Exam lifecycle, papers, grading, result release, and report cards |
| `InvoicesController.php` | Invoice lifecycle, validated offline payment recording, and the disabled online checkout seam |
| `PaymentSubmissionsController.php` | Pending offline claims, private clean evidence downloads, and administrator approval decisions |
| `PaymentsController.php` | Receipts, reversals, refunds, and the disabled online confirmation seam |

## Conventions

* Register specific static routes before routes containing an identifier in `config/routes.php`.
* Resolve records through `findOr404()` or tenant scoped tables. Never query EMS rows without `school_id` scope.
* Call `requireRole()` for officer only actions. Use `Scope` for teacher, parent, and student record limits.
* Return data through `json()` and collection data through `paginated()`.
* Use shared domain engines from `src/Ems/` for calculations and state rules.
* Record consequential changes through the shared audit service.
* Offline payments are the only active collection path. A bursar records a pending claim and a different administrator approves it after cash-batch or statement-row reconciliation; only approval posts ledger money and creates an immutable receipt.
* Accept `cash`, `bank_transfer`, `pos`, or `cheque`. Require clean private evidence and a matched statement row for non-cash methods. Never serve quarantined or rejected evidence.
* Keep online checkout and confirmation unavailable until a server-verified provider adapter replaces the seam. Never trust a browser-submitted payment outcome.

## Gotchas

* Public auth, admissions, and file routes are intentional exceptions to the usual school path.
* The refresh token is an httpOnly cookie. The short lived access token is a Bearer token.

## Related specs

* [`document.md`](../../../document.md)

_Drafted by /audit from the repo, worth a quick human pass. Edit freely: once a line stops matching this draft, later runs treat it as curated and will flag rather than overwrite it._
