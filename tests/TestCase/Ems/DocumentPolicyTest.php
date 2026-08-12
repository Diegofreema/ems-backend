<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\DocumentPolicy;
use App\Ems\Messages;
use App\Ems\Scope;
use App\Ems\Viewer;
use Cake\Http\Exception\ForbiddenException;
use Cake\Utility\Text;

/**
 * The one place the document access rules live (document.md §3.8), shared by the
 * scoped Documents endpoints and the tenant-less file-redemption endpoint so
 * they can never drift apart. Proves the three ownership rules: student-owned
 * defers to family scope, admission-owned is office-only, and verification is
 * office-only (a bursar may read a student document but never verify one).
 */
class DocumentPolicyTest extends EmsDbTestCase
{
    private function viewer(string $role, ?string $userId = null): Viewer
    {
        return new Viewer($this->schoolId, $userId ?? $this->adminId, $role, ucfirst($role));
    }

    private function scopeFor(Viewer $viewer): Scope
    {
        return new Scope($viewer, $this->locator);
    }

    public function testVerificationIsAllowedForTheReviewOffice(): void
    {
        DocumentPolicy::assertCanVerify($this->viewer('administrator'));
        DocumentPolicy::assertCanVerify($this->viewer('registrar'));
        $this->addToAssertionCount(1); // reached here without a refusal
    }

    public function testVerificationIsRefusedForEveryoneElse(): void
    {
        foreach (['bursar', 'teacher', 'parent', 'student'] as $role) {
            try {
                DocumentPolicy::assertCanVerify($this->viewer($role));
                $this->fail("$role must not be able to verify a document");
            } catch (ForbiddenException $e) {
                $this->assertSame(Messages::DOCUMENT_VERIFY_OFFICE_ONLY, $e->getMessage());
            }
        }
    }

    public function testStudentOwnedAccessDefersToFamilyScope(): void
    {
        $ward = $this->seedStudent($this->schoolId);
        $parent = $this->seedUser($this->schoolId, 'parent', ['link_student_ids' => [$ward]]);
        $viewer = $this->viewer('parent', $parent);
        $scope = $this->scopeFor($viewer);

        // Their own ward's document opens.
        DocumentPolicy::assertOwnerAccess($scope, $viewer, 'student', $ward);
        $this->addToAssertionCount(1);

        // Another family's ward is refused with the family 403.
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Messages::STUDENT_FORBIDDEN);
        DocumentPolicy::assertOwnerAccess($scope, $viewer, 'student', Text::uuid());
    }

    public function testAdmissionOwnedAccessIsOfficeOnly(): void
    {
        $adminViewer = $this->viewer('administrator');
        // The office may open an admission document (owner id is irrelevant here).
        DocumentPolicy::assertOwnerAccess($this->scopeFor($adminViewer), $adminViewer, 'application', Text::uuid());
        $this->addToAssertionCount(1);

        // A bursar — whole-school, but not the admissions office — is refused.
        $bursar = $this->viewer('bursar');
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Messages::DOCUMENT_ADMISSION_OFFICE);
        DocumentPolicy::assertOwnerAccess($this->scopeFor($bursar), $bursar, 'application', Text::uuid());
    }
}
