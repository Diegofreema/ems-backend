<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AssignmentsFixture
 */
class AssignmentsFixture extends TestFixture
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
                'subject_id' => 1,
                'student_id' => 1,
                'details' => 'Lorem ipsum dolor sit amet',
                'datecreated' => 1753793438,
                'status' => 'Lorem ipsum do',
                'session_id' => 1,
                'id' => 1,
                'setassignment_id' => 1,
            ],
        ];
        parent::init();
    }
}
