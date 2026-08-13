<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\ORM\Locator\LocatorInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * The tenant-scope choke point (document.md §1.1). Every `ems_` table (except
 * the tenant root `ems_schools`, whose id IS the tenant) carries a `school_id`,
 * and every read of one MUST be narrowed to the current request's school or it
 * leaks — or 404-masks — another tenant's rows.
 *
 * That predicate used to be hand-spelled at ~270 call sites: correct only
 * because each author remembered to add `'school_id' => $viewer->schoolId`.
 * This module makes the rule apply by construction. `query()` hands back a find
 * already narrowed to the tenant, `exists()` folds the predicate into an
 * existence check, and the school_id is bound ONCE (at construction) so callers
 * never name it again. It is the read-side sibling of AppController::findOr404
 * — same philosophy ("the school_id predicate can never be forgotten"), applied
 * to list / filter / aggregate reads rather than by-id lookups.
 *
 * `Tenant` deliberately means "the CURRENT request's tenant." A read that
 * scopes to a DIFFERENT school on purpose — a cross-tenant document grant, an
 * unauthenticated public-by-slug prospectus, or auth's login-by-email across
 * all schools — legitimately does not go through here, and its different shape
 * at the call site is the seam doing its job, not a gap in it.
 *
 * The predicate is emitted table-aliased (`Alias.school_id`) so it is
 * unambiguous under joins / contains — strictly safer than the bare
 * `school_id` most call sites used, and never wrong for a single-table query.
 */
final class Tenant
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var string
     */
    public string $schoolId;

    public function __construct(LocatorInterface $locator, string $schoolId)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
    }

    /**
     * A find over `$table` already narrowed to this tenant. Chain the rest of
     * the query (further where(), order, select, first(), count(), all()) as
     * usual — the school_id predicate is present and correctly aliased.
     */
    public function query(string $table): Query
    {
        $repo = $this->locator->get($table);

        return $repo->find()->where([$repo->getAlias() . '.school_id' => $this->schoolId]);
    }

    /**
     * Tenant-scoped existence check: true when a row in `$table` matches
     * `$conditions` AND belongs to this tenant. The scope predicate is merged
     * first so a caller's conditions can never widen past the tenant.
     *
     * @param array $conditions Extra predicates, in Cake where() form.
     */
    public function exists(string $table, array $conditions = []): bool
    {
        return $this->locator->get($table)
            ->exists(['school_id' => $this->schoolId] + $conditions);
    }

    /**
     * The raw table, for saves and marshaling. Writes still set school_id
     * explicitly on the entity (marshaled data or newEntity), so there is no
     * scoped-write here to forget — the read paths are where the hazard lived.
     */
    public function table(string $table): Table
    {
        return $this->locator->get($table);
    }
}
