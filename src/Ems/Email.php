<?php
declare(strict_types=1);

namespace App\Ems;

/** Renders the small set of transactional EMS e-mails. */
final class Email
{
    /** @return array{text:string,html:string} */
    public static function passwordReset(string $school, string $recipient, string $code): array
    {
        return self::render(
            $school,
            'Password reset',
            'Reset your password',
            sprintf('Hello %s,', $recipient),
            'We received a request to reset the password for your EMS account.',
            sprintf('<div style="margin:28px 0;padding:20px;background:#eef2ff;border-radius:12px;text-align:center;color:#312e81;font:700 32px/1.2 monospace;letter-spacing:8px">%s</div><p style="margin:0;color:#4b5563">Enter this code in EMS within 30 minutes. It can be used once only.</p>', self::escape($code)),
            sprintf("Hello %s,\n\nWe received a request to reset the password for your %s EMS account.\n\nYour reset code is: %s\n\nEnter it within 30 minutes. It can be used once only.\n\nIf you did not request this, you can ignore this e-mail.", $recipient, $school, $code),
            'If you did not request this, you can safely ignore this e-mail. Never share this code with anyone.',
        );
    }

    /** @return array{text:string,html:string} */
    public static function invitation(string $school, string $recipient, string $role, string $url, string $context = ''): array
    {
        $role = ucfirst(strtolower($role));
        $intro = sprintf('%s has invited you to join its portal as a %s.', $school, $role);
        $textIntro = sprintf('%s has invited you to join its EMS portal as a %s.', $school, $role);
        if ($context !== '') {
            // e.g. "for Sam Student (JSS 1A)" — so a parent recognises the mail.
            $intro .= ' ' . sprintf('This access is for %s.', $context);
            $textIntro .= ' ' . sprintf('This access is for %s.', $context);
        }

        return self::render(
            $school,
            'You are invited',
            'Your EMS access is ready',
            sprintf('Hello %s,', $recipient),
            $intro,
            self::button($url, 'Join the portal') . '<p style="margin:24px 0 0;color:#4b5563">This secure invitation expires in 48 hours.</p>',
            sprintf("Hello %s,\n\n%s\n\nOpen this link within 48 hours:\n%s\n\nIf you were not expecting this invitation, you can ignore this e-mail.", $recipient, $textIntro, $url),
            'If you were not expecting this invitation, you can ignore this e-mail.',
        );
    }

    /** @return array{text:string,html:string} */
    public static function verifyEmail(string $school, string $recipient, string $url): array
    {
        return self::render(
            $school,
            'Confirm your e-mail',
            'Verify your e-mail address',
            sprintf('Hello %s,', $recipient),
            sprintf('Thank you for registering %s on EMS. One step remains: confirm that this address is yours.', $school),
            self::button($url, 'Verify my e-mail')
                . '<p style="margin:24px 0 0;color:#4b5563">This secure link expires in 30 minutes. Until the address is verified, the account cannot sign in.</p>',
            sprintf(
                "Hello %s,\n\nThank you for registering %s on EMS. One step remains: confirm that this address is yours.\n\nOpen this link within 30 minutes to verify your e-mail:\n%s\n\nUntil the address is verified, the account cannot sign in. If you did not register this school, you can ignore this e-mail.",
                $recipient,
                $school,
                $url,
            ),
            'If you did not register this school, you can safely ignore this e-mail — nothing will happen without verification.',
        );
    }

    /** @return array{text:string,html:string} */
    public static function welcome(string $school, string $recipient, string $signInUrl): array
    {
        $steps = '<table role="presentation" cellspacing="0" cellpadding="0" style="margin:24px 0 0;width:100%">'
            . self::welcomeStep('1', 'Set up your calendar', 'Create the academic session and terms your school runs on.')
            . self::welcomeStep('2', 'Invite your team', 'Bring in administrators, registrars, bursars and teachers from Settings.')
            . self::welcomeStep('3', 'Enrol your students', 'Admit students one by one, or import your register from a spreadsheet.')
            . '</table>';

        return self::render(
            $school,
            'Account verified',
            sprintf('Welcome aboard, %s', $recipient),
            sprintf('Hello %s,', $recipient),
            sprintf('Your e-mail address is verified and %s is now live on EMS. Everything a school office runs on — admissions, classes, examinations, fees and communication — is ready for you.', $school),
            self::button($signInUrl, 'Open your dashboard') . $steps,
            sprintf(
                "Hello %s,\n\nYour e-mail address is verified and %s is now live on EMS.\n\nHere is how most schools begin:\n\n1. Set up your calendar — create the academic session and terms.\n2. Invite your team — administrators, registrars, bursars and teachers.\n3. Enrol your students — admit them one by one or import a spreadsheet.\n\nSign in any time:\n%s\n\nWe are glad to have %s with us.",
                $recipient,
                $school,
                $signInUrl,
                $school,
            ),
            'You are receiving this message because this address was verified for a school account on EMS.',
        );
    }

    /** One numbered row of the welcome e-mail's getting-started list. */
    private static function welcomeStep(string $number, string $title, string $detail): string
    {
        return '<tr><td style="padding:10px 0;vertical-align:top;width:44px"><div style="width:32px;height:32px;border-radius:16px;background:#eef2ff;color:#4f46e5;font-weight:700;text-align:center;line-height:32px">'
            . self::escape($number) . '</div></td><td style="padding:10px 0 10px 4px"><div style="font-weight:700;color:#111827">'
            . self::escape($title) . '</div><div style="margin-top:2px;color:#4b5563;font-size:14px;line-height:1.5">'
            . self::escape($detail) . '</div></td></tr>';
    }

    /** @return array{text:string,html:string} The 2FA sign-in code e-mail. */
    public static function loginCode(string $school, string $recipient, string $code): array
    {
        return self::render(
            $school,
            'Sign-in code',
            'Your sign-in code',
            sprintf('Hello %s,', $recipient),
            sprintf('Use this code to finish signing in to %s on EMS.', $school),
            sprintf('<div style="margin:28px 0;padding:20px;background:#eef2ff;border-radius:12px;text-align:center;color:#312e81;font:700 32px/1.2 monospace;letter-spacing:8px">%s</div><p style="margin:0;color:#4b5563">Enter this code to continue. It expires in 10 minutes and can be used once.</p>', self::escape($code)),
            sprintf("Hello %s,\n\nUse this code to finish signing in to %s on EMS.\n\nYour sign-in code is: %s\n\nIt expires in 10 minutes and can be used once.\n\nIf you did not just try to sign in, change your password — someone may know it.", $recipient, $school, $code),
            'If you did not just try to sign in, someone may know your password — change it right away. Never share this code with anyone.',
        );
    }

    /** @return array{text:string,html:string} The confirm-new-address e-mail. */
    public static function emailChange(string $school, string $recipient, string $newEmail, string $url): array
    {
        return self::render(
            $school,
            'Confirm your e-mail',
            'Confirm your new e-mail',
            sprintf('Hello %s,', $recipient),
            sprintf('A request was made to change the sign-in e-mail for your %s account on EMS to %s. Confirm it is yours to complete the change.', $school, $newEmail),
            self::button($url, 'Confirm this e-mail')
                . '<p style="margin:24px 0 0;color:#4b5563">This secure link expires in 30 minutes. Until you confirm, your current e-mail keeps working.</p>',
            sprintf(
                "Hello %s,\n\nA request was made to change the sign-in e-mail for your %s account on EMS to %s.\n\nConfirm it is yours by opening this link within 30 minutes:\n%s\n\nUntil you confirm, your current e-mail keeps working. If you did not request this, you can ignore this e-mail.",
                $recipient,
                $school,
                $newEmail,
                $url,
            ),
            'If you did not request this change, you can safely ignore this e-mail — nothing will change without confirmation.',
        );
    }

    /** @return array{text:string,html:string} */
    public static function update(
        string $school,
        string $recipient,
        ?string $student,
        string $subject,
        string $body,
    ): array {
        $context = $student === null || $student === ''
            ? ''
            : '<p style="margin:0 0 20px;color:#4b5563"><strong>Regarding:</strong> ' . self::escape($student) . '</p>';

        return self::render(
            $school,
            'School update',
            $subject,
            sprintf('Hello %s,', $recipient),
            'Here is the latest update from your school.',
            $context . '<div style="color:#374151;font-size:16px;line-height:1.65">' . nl2br(self::escape($body)) . '</div>',
            sprintf("Hello %s,\n\n%s\n\n%s%s\n\n%s", $recipient, $subject, $student === null || $student === '' ? '' : 'Regarding: ' . $student . "\n\n", $body, $school),
            'You are receiving this message from your school through EMS.',
        );
    }

    /**
     * The acknowledgement sent to a prospect who booked a demo.
     *
     * @return array{text:string,html:string}
     */
    public static function demoRequestAck(string $contact, string $institution): array
    {
        return self::render(
            'EMS',
            'Demo request received',
            'Thanks for your interest',
            sprintf('Hello %s,', $contact),
            sprintf('We have received your request for a demo of EMS for %s.', $institution),
            '<p style="margin:0;color:#4b5563;font-size:16px;line-height:1.6">A member of our team will reach out shortly to arrange a time that suits you. There is nothing more you need to do for now.</p>',
            sprintf("Hello %s,\n\nWe have received your request for a demo of EMS for %s.\n\nA member of our team will reach out shortly to arrange a time that suits you. There is nothing more you need to do for now.\n\nThank you,\nThe EMS team", $contact, $institution),
            'You are receiving this message because a demo of EMS was requested with this e-mail address.',
        );
    }

    /**
     * The internal notification e-mailed to the team for each demo request.
     * Reply-To is set to the prospect (see Resend::deliver), so the details
     * table here is for the team to read and act on.
     *
     * @param array<string,string> $fields Ordered label => value; blanks dropped.
     * @return array{text:string,html:string}
     */
    public static function demoRequestNotify(string $institution, array $fields): array
    {
        $rows = '';
        $lines = '';
        foreach ($fields as $label => $value) {
            if (trim($value) === '') {
                continue;
            }
            $rows .= self::detailRow($label, $value);
            $lines .= sprintf("%s: %s\n", $label, $value);
        }

        return self::render(
            'EMS',
            'New demo request',
            $institution,
            'A new demo request has arrived.',
            'Reply to this e-mail to reach the contact directly.',
            '<table role="presentation" cellspacing="0" cellpadding="0" style="width:100%;margin:8px 0 0">' . $rows . '</table>',
            sprintf("A new demo request has arrived.\n\n%s\nReply to this e-mail to reach the contact directly.", $lines),
            'Sent by the EMS book-a-demo form.',
        );
    }

    /** One label/value row of the demo-request details table. */
    private static function detailRow(string $label, string $value): string
    {
        return '<tr>'
            . '<td style="padding:8px 12px 8px 0;vertical-align:top;color:#6b7280;font-size:13px;font-weight:700;white-space:nowrap">'
            . self::escape($label) . '</td>'
            . '<td style="padding:8px 0;vertical-align:top;color:#111827;font-size:15px;line-height:1.5">'
            . nl2br(self::escape($value)) . '</td>'
            . '</tr>';
    }

    /** @return array{text:string,html:string} */
    private static function render(
        string $school,
        string $eyebrow,
        string $heading,
        string $greeting,
        string $lead,
        string $content,
        string $text,
        string $notice,
    ): array {
        $school = self::escape($school);
        $html = '<!doctype html><html lang="en"><body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px"><tr><td align="center">'
            . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden">'
            . '<tr><td style="padding:28px 36px;background:#111827;color:#ffffff"><div style="font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;color:#a5b4fc">EMS</div><div style="margin-top:7px;font-size:21px;font-weight:700">' . $school . '</div></td></tr>'
            . '<tr><td style="padding:36px"><div style="font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;color:#4f46e5">' . self::escape($eyebrow) . '</div>'
            . '<h1 style="margin:10px 0 18px;font-size:28px;line-height:1.2">' . self::escape($heading) . '</h1>'
            . '<p style="margin:0 0 12px;font-size:16px;line-height:1.6">' . self::escape($greeting) . '</p>'
            . '<p style="margin:0 0 24px;color:#4b5563;font-size:16px;line-height:1.6">' . self::escape($lead) . '</p>'
            . $content
            . '<p style="margin:28px 0 0;padding-top:20px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;line-height:1.5">' . self::escape($notice) . '</p>'
            . '</td></tr><tr><td style="padding:20px 36px;background:#f9fafb;color:#6b7280;font-size:12px;line-height:1.5">Sent by ' . $school . ' through EMS.</td></tr>'
            . '</table></td></tr></table></body></html>';

        return ['text' => $text, 'html' => $html];
    }

    /** Render a consistently styled action link. */
    private static function button(string $url, string $label): string
    {
        return '<p style="margin:28px 0"><a href="' . self::escape($url) . '" style="display:inline-block;padding:14px 20px;background:#4f46e5;border-radius:8px;color:#ffffff;font-weight:700;text-decoration:none">' . self::escape($label) . '</a></p>';
    }

    /** Escape untrusted message content for the HTML body. */
    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Static utility class. */
    private function __construct()
    {
    }
}
