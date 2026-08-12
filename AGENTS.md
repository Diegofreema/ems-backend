# LTALMS and EMS backend

## Stack

* **Language and runtime**: PHP 8.2 or newer
* **Framework**: CakePHP 5.4 with CakePHP Migrations 5
* **Database**: MySQL through CakePHP ORM
* **Key dependencies**: PHPUnit, DOMPDF, PhpSpreadsheet, chillerlan QR Code
* **Package manager**: Composer

## Build approach

Journey: complete one production user path, including failure and recovery states, before opening the next.

## Commands

```bash
# Install
composer install

# Development server
php bin/cake.php server -p 8765

# Apply migrations
php bin/cake.php migrations migrate

# Test
composer test

# Style check
composer cs-check
```

## Rules

* Treat the existing web application, `/api/v1`, and `/api/ems` as separate surfaces. Change only the surface in scope.
* The React EMS product consumes `/api/ems`. Its wire contract is recorded in `document.md`.
* Keep every EMS query tenant scoped through `App\Ems\Tenant`. Apply viewer limits through `App\Ems\Scope`.
* Keep HTTP policy, authentication, CORS, pagination, and error handling in `src/Controller/Ems/AppController.php`.
* Put reusable domain computations in `src/Ems/`. Controllers should coordinate requests and responses.
* Preserve response shapes through the serializers in `src/Ems/Serializer/`.
* Store money as integer minor units. Keep dates and times in the contract formats.
* Add schema changes as ordered CakePHP migrations. Do not edit an applied migration.
* Add or update focused EMS tests for contract, tenancy, policy, authentication, and domain rule changes.
* In production, `EMS_FRONTEND_URL` is the canonical React URL for invitation links and the default exact CORS origin; use `EMS_CORS_ORIGINS` only for intentional additional frontends.

## Context files

* [src/Controller/Ems/AGENTS.md](src/Controller/Ems/AGENTS.md): EMS HTTP controllers, authorization, and wire behavior
* [src/Ems/AGENTS.md](src/Ems/AGENTS.md): EMS domain services, tenancy, policy, and serialization

_Drafted by /audit from the repo, worth a quick human pass. Edit freely: once a line stops matching this draft, later runs treat it as curated and will flag rather than overwrite it._
