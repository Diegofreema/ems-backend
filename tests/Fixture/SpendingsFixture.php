<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SpendingsFixture
 */
class SpendingsFixture extends TestFixture
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
                'amount' => 'Lorem ipsum dolor ',
                'description' => 'Lorem ipsum dolor sit amet',
                'datecreated' => 1777016946,
            ],
        ];
        parent::init();
    }
}
