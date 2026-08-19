<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\FamilyInvites;
use App\Ems\Messages;
use Cake\Http\Response;

/**
 * Bulk parent-portal onboarding (§3.19). The plan lists every family the
 * school could invite — grouped so one parent of three children is one
 * account — and creation runs in small chunks the frontend loops, so 5000
 * families need no queue infrastructure.
 */
class FamilyInvitesController extends AppController
{
    /**
     * GET /family-invites/plan?classGroupId= — FamilyInvitePlan, unpaginated.
     */
    public function plan(): Response
    {
        $classGroupId = trim((string)$this->request->getQuery('classGroupId', ''));
        $service = new FamilyInvites($this->getTableLocator(), $this->viewer->schoolId);

        return $this->json($service->plan($classGroupId === '' ? null : $classGroupId));
    }

    /**
     * POST /family-invites { guardianIds } — up to 25 per request; per-target
     * outcomes, raw codes only where no mailbox could receive the link.
     */
    public function create(): Response
    {
        $ids = $this->body()['guardianIds'] ?? null;
        $ids = is_array($ids) ? array_values(array_unique(array_filter(array_map('strval', $ids)))) : [];
        if ($ids === []) {
            $this->fail(422, Messages::FAMILY_INVITE_IDS_REQUIRED);
        }
        if (count($ids) > FamilyInvites::BATCH_LIMIT) {
            $this->fail(422, Messages::FAMILY_INVITE_BATCH_LIMIT);
        }

        $service = new FamilyInvites($this->getTableLocator(), $this->viewer->schoolId);

        return $this->json(['results' => $service->create($ids)], 201);
    }
}
