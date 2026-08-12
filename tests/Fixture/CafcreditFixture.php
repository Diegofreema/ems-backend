<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CafcreditFixture
 */
class CafcreditFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'cafcredit';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'matricnum' => 'Lorem ipsum dolor sit amet',
                'amount' => 1,
                'date1' => '2022-11-16 07:39:33',
                'id' => 1,
            ],
        ];
        parent::init();
    }
}
