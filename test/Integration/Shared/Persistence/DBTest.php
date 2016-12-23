<?php
declare(strict_types=1);

namespace Test\Integration\Shared\Persistence;

use Ramsey\Uuid\Uuid;
use Shared\Persistence\DB;

class DBTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        putenv('SERVICE_NAME=integration_test');
        @unlink(DB::databaseFilePath());
    }

    /**
     * @test
     */
    public function it_persists_and_retrieves_objects_by_their_id()
    {
        $id = Uuid::uuid4();
        $object = new PersistableDummy($id);

        DB::persist($object);

        $retrievedObject = DB::retrieve(get_class($object), $id);

        $this->assertEquals($object, $retrievedObject);
    }
}
