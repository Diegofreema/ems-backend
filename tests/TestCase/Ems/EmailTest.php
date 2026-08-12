<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\Email;
use Cake\TestSuite\TestCase;

class EmailTest extends TestCase
{
    public function testActionMessagesIdentifyTheSchoolAndEscapeDynamicContent(): void
    {
        $reset = Email::passwordReset('Ada Academy', 'Ada', '123456');
        $invite = Email::invitation('Ada Academy', 'Sam', 'teacher', 'https://ems.test/join?code=A&B');
        $update = Email::update('Ada Academy', 'Pat', 'Ayo', 'Open <day>', "Bring <books>.\nDoors open at 9.");

        $this->assertStringContainsString('Ada Academy', $reset['html']);
        $this->assertStringContainsString('123456', $reset['html']);
        $this->assertStringContainsString('Join the portal', $invite['html']);
        $this->assertStringContainsString('code=A&amp;B', $invite['html']);
        $this->assertStringContainsString('Regarding:</strong> Ayo', $update['html']);
        $this->assertStringContainsString('Open &lt;day&gt;', $update['html']);
        $this->assertStringContainsString('Bring &lt;books&gt;.', $update['html']);
    }
}
