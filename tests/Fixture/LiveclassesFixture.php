<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LiveclassesFixture
 */
class LiveclassesFixture extends TestFixture
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
                'meetinglink' => 'Lorem ipsum dolor sit amet',
                'teacher_id' => 1,
                'datecreated' => 1754048858,
            ],
        ];
        parent::init();
    }
}
