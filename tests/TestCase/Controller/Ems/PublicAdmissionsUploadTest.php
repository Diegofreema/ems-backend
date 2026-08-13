<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Storage;
use Cake\Cache\Cache;
use Cake\Utility\Text;

final class PublicAdmissionsUploadTest extends EmsIntegrationTestCase
{
    protected const CLEANUP_TABLES = [
        'ems_document_objects',
        'ems_documents',
        'ems_admission_applications',
        'ems_admission_cycles',
        'ems_sequences',
        'ems_users',
        'ems_schools',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        $this->seedOpenCycle();
    }

    protected function tearDown(): void
    {
        Cache::clear();
        parent::tearDown();
    }

    public function testPublicApplicationRejectsAnOverlimitBodyThatClaimsToBeSmall(): void
    {
        $body = 'data:application/pdf;base64,' . base64_encode(
            str_repeat('A', Storage::MAX_UPLOAD_BYTES + 1),
        );

        $this->post($this->applyPath(), $this->applicationWithDocument([
            'name' => 'Birth certificate',
            'type' => 'birth_certificate',
            'contentType' => 'application/pdf',
            'sizeBytes' => 1,
            'body' => $body,
        ]));

        $this->assertResponseCode(413);
        $this->assertSame(Messages::FILE_TOO_LARGE, $this->responseJson()['message']);
        $this->assertSame(0, $this->rowCount('ems_admission_applications', ['school_id' => $this->schoolId]));
        $this->assertSame(0, $this->rowCount('ems_documents', ['school_id' => $this->schoolId]));
        $this->assertSame(0, $this->rowCount('ems_document_objects', []));
    }

    public function testPublicApplicationAcceptsAValidSmallDataUrlUpload(): void
    {
        $this->post($this->applyPath(), $this->applicationWithDocument([
            'name' => 'Birth certificate',
            'type' => 'birth_certificate',
            'contentType' => 'application/pdf',
            'sizeBytes' => 5,
            'body' => 'data:application/pdf;base64,JVBERi0=',
        ]));

        $this->assertResponseCode(201);
        $document = $this->db->selectQuery()
            ->select(['size_bytes'])
            ->from('ems_documents')
            ->where(['school_id' => $this->schoolId])
            ->execute()
            ->fetch('assoc');
        $this->assertSame(5, (int)$document['size_bytes']);
        $this->assertSame(1, $this->rowCount('ems_documents', ['school_id' => $this->schoolId]));
        $this->assertSame(1, $this->rowCount('ems_document_objects', []));
    }

    private function applyPath(): string
    {
        return '/api/ems/public/schools/' . $this->schoolId . '/apply';
    }

    private function applicationWithDocument(array $document): array
    {
        return [
            'firstName' => 'Amina',
            'lastName' => 'Applicant',
            'documents' => [$document],
        ];
    }

    private function seedOpenCycle(): void
    {
        $this->insertRow('ems_admission_cycles', [
            'id' => Text::uuid(),
            'school_id' => $this->schoolId,
            'name' => 'Current intake',
            'session' => '2026/2027',
            'opens_on' => date('Y-m-d', strtotime('-1 day')),
            'closes_on' => date('Y-m-d', strtotime('+1 day')),
            'status' => 'open',
        ]);
    }
}
