<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PostsFixture
 */
class PostsFixture extends TestFixture
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
                'postcategory_id' => 1,
                'user_id' => 1,
                'allowcomments' => 'Lorem ',
                'title' => 'Lorem ipsum dolor sit amet',
                'postbody' => 'Lorem ipsum dolor sit amet',
                'status' => 'Lorem ipsum ',
                'dateadded' => 1651230442,
                'lastedited' => 1651230442,
            ],
        ];
        parent::init();
    }
}
