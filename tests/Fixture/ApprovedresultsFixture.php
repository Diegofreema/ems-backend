<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApprovedresultsFixture
 */
class ApprovedresultsFixture extends TestFixture
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
                'session_id' => 1,
                'semester_id' => 1,
                'status' => 'Lorem ipsum dolor sit amet',
                'admin_id' => 1,
            ],
        ];
        parent::init();
    }
}
