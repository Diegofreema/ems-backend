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
    public static function invitation(string $school, string $recipient, string $role, string $url): array
    {
        $role = ucfirst(strtolower($role));

        return self::render(
            $school,
            'You are invited',
            'Your EMS access is ready',
            sprintf('Hello %s,', $recipient),
            sprintf('%s has invited you to join its portal as a %s.', $school, $role),
            self::button($url, 'Join the portal') . '<p style="margin:24px 0 0;color:#4b5563">This secure invitation expires in 48 hours.</p>',
            sprintf("Hello %s,\n\n%s has invited you to join its EMS portal as a %s.\n\nOpen this link within 48 hours:\n%s\n\nIf you were not expecting this invitation, you can ignore this e-mail.", $recipient, $school, $role, $url),
            'If you were not expecting this invitation, you can ignore this e-mail.',
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
