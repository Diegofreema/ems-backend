<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SponsorshipsFixture
 */
class SponsorshipsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'sponsor_id' => 1,
                'session_id' => 1,
                'student_id' => 1,
                'admin_id' => 1,
                'datecreated' => 1684762031,
            ],
        ];
        parent::init();
    }
}
