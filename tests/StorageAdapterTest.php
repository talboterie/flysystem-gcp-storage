<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage\Tests;

use Prophecy\Argument;
use League\Flysystem\Config;
use PHPUnit\Framework\TestCase;
use Google\Cloud\Storage\Bucket;
use Prophecy\Prophecy\ObjectProphecy;
use Google\Cloud\Storage\StorageObject;
use Talboterie\FlysystemGCPStorage\StorageAdapter;
use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Storage\Connection\ConnectionInterface;

class StorageAdapterTest extends TestCase
{
    /** @var Bucket|ObjectProphecy */
    protected $client;

    /** @var ConnectionInterface|ObjectProphecy */
    protected $connection;

    /** @var StorageAdapter */
    protected $storageAdapter;

    /** @before */
    public function setupAdapter(): void
    {
        $this->client = $this->prophesize(Bucket::class);
        $this->connection = $this->prophesize(ConnectionInterface::class);

        $this->storageAdapter = new StorageAdapter($this->client->reveal(), 'prefix');
    }

    private function createStorageObject(string $name, string $bucket = 'bucket', array $meta = []): StorageObject
    {
        return new StorageObject($this->connection->reveal(), $name, $bucket, null, $meta);
    }

    /** @test */
    public function itCanWriteAnObject(): void
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->write('something', 'contents', new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanWriteAStream()
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->writeStream('something', tmpfile(), new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanUpdateAnObject()
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->update('something', 'contents', new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanUpdateAStream()
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->updateStream('something', tmpfile(), new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanRenameAnObject()
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->copy(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createStorageObject('newthing'));

        $object
            ->delete()
            ->willReturn();

        $this->client
            ->name()
            ->willReturn('bucket');

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->rename('something', 'newthing');

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('newthing', $result->name());
    }

    /** @test */
    public function itCanCopyAnObject()
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->copy(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createStorageObject('newthing'));

        $this->client
            ->name()
            ->willReturn('bucket');

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->copy('something', 'newthing');

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('newthing', $result->name());
    }

    /** @test */
    public function itCanDeleteAnObject()
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->delete()
            ->willReturn();

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->delete('something');

        $this->assertTrue($result);
    }

    /** @test */
    public function itCannotDeleteAnNonExistentObject()
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->delete()
            ->willThrow(NotFoundException::class);

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->delete('not_found');

        $this->assertFalse($result);
    }
}
