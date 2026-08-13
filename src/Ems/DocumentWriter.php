<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\HttpException;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorInterface;
use Cake\Utility\Text;

/**
 * Writes a document: validates the upload, puts the bytes in the private
 * bucket, and records the metadata row. Shared by the Documents endpoint and
 * the public application-submit flow (which stores files inside its own
 * transaction) so both go through the same validation and layout.
 *
 * Equivalent to the mock's `storeUpload`.
 */
class DocumentWriter
{
    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var \App\Ems\Storage
     */
    private Storage $storage;

    public function __construct(LocatorInterface $locator, Storage $storage)
    {
        $this->locator = $locator;
        $this->storage = $storage;
    }

    /**
     * @param array{name:string,type:string,contentType:string,sizeBytes:int,body:string} $upload
     */
    public function store(string $schoolId, string $owner, string $ownerId, array $upload, string $uploader): EntityInterface
    {
        $contentType = (string)($upload['contentType'] ?? '');
        $sizeBytes = (int)($upload['sizeBytes'] ?? 0);
        $this->storage->assertAcceptable($contentType, $sizeBytes);

        $name = trim((string)($upload['name'] ?? ''));
        if ($name === '') {
            throw new HttpException(Messages::DOCUMENT_NAME_REQUIRED, 422);
        }

        $documents = $this->locator->get('EmsDocuments');
        $id = Text::uuid();
        $storagePath = $this->storage->storagePathFor($schoolId, $owner, $ownerId, $id);
        $this->storage->putObject($storagePath, $contentType, $sizeBytes, (string)($upload['body'] ?? ''));

        $document = $documents->newEntity([
            'school_id' => $schoolId,
            'owner' => $owner,
            'owner_id' => $ownerId,
            'name' => $name,
            'type' => (string)($upload['type'] ?? 'other'),
            'content_type' => $contentType,
            'size_bytes' => $sizeBytes,
            'storage_path' => $storagePath,
            'uploaded_by' => $uploader,
            'uploaded_on' => FrozenDate::today(),
            'verification' => 'pending',
        ], ['validate' => false]);
        $document->id = $id; // control the id so it matches the storage path
        $documents->saveOrFail($document);

        return $document;
    }
}
