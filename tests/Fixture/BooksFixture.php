<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BooksFixture
 */
class BooksFixture extends TestFixture
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
                'title' => 'Lorem ipsum dolor sit amet',
                'author' => 'Lorem ipsum dolor sit amet',
                'pubdate' => 'Lorem ipsum dolor sit amet',
                'isavailable' => 'Lorem ipsum dolo',
                'date_created' => 1753268900,
                'user_id' => 1,
                'isbn' => 'Lorem ipsum dolor sit amet',
                'coverphoto' => 'Lorem ipsum dolor sit amet',
                'copies' => 1,
                'section' => 'Lorem ipsum dolor sit amet',
                'callno' => 'Lorem ipsum dolor si',
                'department_id' => 1,
            ],
        ];
        parent::init();
    }
}
