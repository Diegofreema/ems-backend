<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Tenant;

/**
 * The tenant-scope choke point (document.md §1.1). Proves the school_id
 * predicate is present by construction on every read — a query is narrowed to
 * its tenant, an existence check cannot be widened past it by caller conditions,
 * and one tenant can never see another's rows even when the data looks alike.
 */
class TenantTest extends EmsDbTestCase
{
    public function testQueryReturnsOnlyThisTenantsRows(): void
    {
        $other = $this->seedSchool();
        $this->seedStudent($this->schoolId, ['first_name' => 'Ada']);
        $this->seedStudent($other, ['first_name' => 'Ben']);

        $names = (new Tenant($this->locator, $this->schoolId))
            ->query('EmsStudents')
            ->all()
            ->extract('first_name')
            ->toList();

        $this->assertSame(['Ada'], $names);
    }

    public function testQueryStaysNarrowedUnderFurtherConditionsAndAggregates(): void
    {
        $other = $this->seedSchool();
        $this->seedStudent($this->schoolId, ['class_group' => 'JSS 1A']);
        $this->seedStudent($this->schoolId, ['class_group' => 'JSS 1A']);
        $this->seedStudent($this->schoolId, ['class_group' => 'JSS 2B']);
        // A same-named class in the OTHER tenant must not leak into the count.
        $this->seedStudent($other, ['class_group' => 'JSS 1A']);

        $count = (new Tenant($this->locator, $this->schoolId))
            ->query('EmsStudents')
            ->where(['class_group' => 'JSS 1A'])
            ->count();

        $this->assertSame(2, $count);
    }

    public function testExistsIsScopedAndCannotBeWidenedPastTheTenant(): void
    {
        $other = $this->seedSchool();
        // The SAME admission number in another tenant — the condition matches a
        // row, but not one this tenant owns.
        $this->seedStudent($other, ['admission_number' => 'ADM-XYZ']);

        $tenant = new Tenant($this->locator, $this->schoolId);
        $this->assertFalse($tenant->exists('EmsStudents', ['admission_number' => 'ADM-XYZ']));

        // The same row inside this tenant is found.
        $this->seedStudent($this->schoolId, ['admission_number' => 'ADM-XYZ']);
        $this->assertTrue($tenant->exists('EmsStudents', ['admission_number' => 'ADM-XYZ']));
    }

    public function testTableReturnsTheRawRepository(): void
    {
        $table = (new Tenant($this->locator, $this->schoolId))->table('EmsStudents');
        $this->assertSame('EmsStudents', $table->getAlias());
    }
}
