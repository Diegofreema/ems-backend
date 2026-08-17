<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\FeeReminders;
use App\Ems\Messages;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Fee reminders (document.md §3.7). A bursar sends overdue or due-soon nudges to
 * owing families — an in-app portal row and an e-mail to the primary guardian.
 * Not a money write, so no two-person decision and no idempotency key: the
 * 7-day per-instalment cooldown (App\Ems\FeeReminders) is what stops a repeat or
 * double-click from re-nagging the same family. Every send is audit-logged.
 */
final class FeeRemindersController extends AppController
{
    /** POST /fee-reminders/preview — who would be reminded, and how many are suppressed. */
    public function preview(): Response
    {
        $kind = (string)($this->body()['kind'] ?? '');
        if (!FeeReminders::isKind($kind)) {
            $this->fail(422, Messages::REMINDER_KIND_INVALID);
        }

        return $this->json($this->reminders()->preview($kind));
    }

    /** POST /fee-reminders — send the reminders for the chosen kind. */
    public function send(): Response
    {
        if ($this->viewer->role !== 'bursar') {
            $this->fail(403, 'Only a bursar can send fee reminders.');
        }
        $kind = (string)($this->body()['kind'] ?? '');
        if (!FeeReminders::isKind($kind)) {
            $this->fail(422, Messages::REMINDER_KIND_INVALID);
        }
        $summary = $this->reminders()->send(
            $kind,
            $this->comms(),
            (string)Configure::read('Ems.frontendBaseUrl', ''),
            $this->viewer->name,
        );
        $this->audit()->log(
            $this->viewer,
            'fee_reminder.sent',
            'fee_reminder',
            $kind,
            sprintf(
                'Sent %d %s fee reminder%s (%d suppressed by the cooldown).',
                (int)$summary['remindedCount'],
                $kind === FeeReminders::OVERDUE ? 'overdue' : 'due-soon',
                (int)$summary['remindedCount'] === 1 ? '' : 's',
                (int)$summary['suppressedCount'],
            ),
        );

        return $this->json($summary);
    }

    /** GET /fee-reminders — the recent send log, newest first. */
    public function index(): Response
    {
        $items = [];
        foreach (
            $this->tenant()->query('EmsNotifications')
                ->where(['kind' => FeeReminders::KIND])
                ->orderByDesc('created')
                ->limit(50)
                ->all() as $n
        ) {
            $items[] = [
                'id' => (string)$n->id,
                'subject' => (string)$n->subject,
                'audienceLabel' => (string)$n->audience_label,
                'recipientCount' => (int)$n->recipient_count,
                'sentOn' => (string)$n->sent_on,
                'sentBy' => (string)$n->sent_by,
            ];
        }

        return $this->json([
            'items' => $items,
            'total' => count($items),
            'page' => 1,
            'pageSize' => count($items),
        ]);
    }

    private function reminders(): FeeReminders
    {
        return new FeeReminders(
            $this->getTableLocator(),
            $this->viewer->schoolId,
            FrozenDate::today()->format('Y-m-d'),
        );
    }
}
