<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FeesFixture
 */
class FeesFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'amount' => 1,
                'user_id' => 1,
                'status' => 1,
                'startdate' => 'Lorem ipsum dolor sit amet',
                'enddate' => 'Lorem ipsum dolor sit amet',
                'feetype' => 'Lorem ipsum dolor sit amet',
                'itemcode' => 'Lorem ipsum dolor si',
            ],
        ];
        parent::init();
    }
}
