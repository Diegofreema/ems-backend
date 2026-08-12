<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FeesSponsorshipsFixture
 */
class FeesSponsorshipsFixture extends TestFixture
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
                'fee_id' => 1,
                'sponsorship_id' => 1,
            ],
        ];
        parent::init();
    }
}
