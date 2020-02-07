<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage\Tests;

use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Storage\Acl;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\Connection\ConnectionInterface;
use Google\Cloud\Storage\StorageObject;
use League\Flysystem\Config;
use LogicException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\StreamInterface;
use Talboterie\FlysystemGCPStorage\StorageAdapter;
use Talboterie\FlysystemGCPStorage\StorageVisibility;

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
    public function itCanWriteAStream(): void
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->writeStream('something', tmpfile(), new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanUpdateAnObject(): void
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->update('something', 'contents', new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanUpdateAStream(): void
    {
        $this->client
            ->upload(Argument::any(), Argument::type('array'))
            ->willReturn($this->createStorageObject('something'));

        $result = $this->storageAdapter->updateStream('something', tmpfile(), new Config());

        $this->assertEquals(StorageObject::class, get_class($result));
        $this->assertEquals('something', $result->name());
    }

    /** @test */
    public function itCanRenameAnObject(): void
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
    public function itCanCopyAnObject(): void
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
    public function itCanDeleteAnObject(): void
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
    public function itCannotDeleteAnNonExistentObject(): void
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

    /** @test */
    public function itCanDeleteADirectory(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->delete()
            ->willReturn();
        $object
            ->name()
            ->willReturn('prefix/something');

        $this->client
            ->object(Argument::type('string'), Argument::type('array'))
            ->willReturn($object);

        $this->client
            ->objects(Argument::type('array'))
            ->willReturn([$object->reveal()]);

        $result = $this->storageAdapter->deleteDir('something');

        $this->assertTrue($result);
    }

    /** @test */
    public function itCannotCreateADirectory(): void
    {
        $this->expectException(LogicException::class);

        $this->storageAdapter->createDir('something', new Config());
    }

    /** @test */
    public function itHasObject(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->exists()
            ->willReturn(true);

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->has('something');

        $this->assertTrue($result);
    }

    /** @test */
    public function itHasntObject(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->exists()
            ->willReturn(false);

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->has('not_found');

        $this->assertFalse($result);
    }

    /** @test */
    public function itCanReadAnObject(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->downloadAsString()
            ->willReturn('contents');

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->read('something');

        $this->assertEquals('contents', $result['contents']);
    }

    /** @test */
    public function itCanReadAStream(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->downloadAsStream()
            ->willReturn($this->prophesize(StreamInterface::class)->reveal());

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object->reveal());

        $result = $this->storageAdapter->readStream('something');

        $this->assertInstanceOf(StreamInterface::class, $result['stream']);
    }

    /** @test */
    public function itCanListObjectsOfABucket(): void
    {
        $this->client
            ->objects(Argument::type('array'))
            ->willReturn([
                $this->createStorageObject('something'),
                $this->createStorageObject('newthing'),
            ]);

        $result = $this->storageAdapter->listContents();

        $this->assertIsArray($result);
        $this->assertEquals('something', $result[0]['path']);
        $this->assertEquals('newthing', $result[1]['path']);
    }

    /** @test */
    public function itCanListSubObjectsOfABucket(): void
    {
        $this->client
            ->objects(Argument::type('array'))
            ->willReturn([$this->createStorageObject('dir/something')]);

        $result = $this->storageAdapter->listContents('dir');

        $this->assertIsArray($result);
        $this->assertEquals('dir/something', $result[0]['path']);
    }

    /** @test */
    public function itCanFetchMetaOfAnObject(): void
    {
        $this->client
            ->object(Argument::type('string'))
            ->willReturn($this->createStorageObject('something', 'bucket', ['size' => 1024]));

        $result = $this->storageAdapter->getMetadata('something');

        $this->assertEquals(1024, $result['size']);
    }

    /** @test */
    public function itCanFetchSizeOfAnObject(): void
    {
        $this->client
            ->object(Argument::type('string'))
            ->willReturn($this->createStorageObject('something', 'bucket', ['size' => 1024]));

        $result = $this->storageAdapter->getSize('something');

        $this->assertEquals(1024, $result['size']);
    }

    /** @test */
    public function itCanFetchMimeTypeOfAnObject(): void
    {
        $this->client
            ->object(Argument::type('string'))
            ->willReturn($this->createStorageObject('something', 'bucket', ['contentType' => 'text/plain']));

        $result = $this->storageAdapter->getMimetype('something');

        $this->assertEquals('text/plain', $result['mimetype']);
    }

    /** @test */
    public function itCanFetchTimestampOfAnObject(): void
    {
        $this->client
            ->object(Argument::type('string'))
            ->willReturn($this->createStorageObject('something', 'bucket', ['updated' => '2020-02-06 23:28:32']));

        $result = $this->storageAdapter->getTimestamp('something');

        $this->assertEquals(strtotime('2020-02-06 23:28:32'), $result['timestamp']);
    }

    /** @test */
    public function itCanSetVisibilityOfAnObject(): void
    {
        $object = $this->prophesize(StorageObject::class);
        $object
            ->update(Argument::type('array'), Argument::type('array'))
            ->willReturn();

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object);

        $result = $this->storageAdapter->setVisibility('something', StorageVisibility::PUBLIC_READ);

        $this->assertEquals(StorageVisibility::PUBLIC_READ, $result['visibility']);
    }

    /** @test */
    public function itCanGetVisibilityOfAnObject(): void
    {
        $acl = $this->prophesize(Acl::class);
        $acl->get()
            ->willReturn([['entity' => 'allUsers', 'role' => 'READER']]);

        $object = $this->prophesize(StorageObject::class);
        $object
            ->acl()
            ->willReturn($acl);

        $this->client
            ->object(Argument::type('string'))
            ->willReturn($object);

        $result = $this->storageAdapter->getVisibility('something');

        $this->assertEquals('allUsers', $result['visibility'][0]['entity']);
        $this->assertEquals('READER', $result['visibility'][0]['role']);
    }

    private function createStorageObject(string $name, string $bucket = 'bucket', array $meta = []): StorageObject
    {
        return new StorageObject($this->connection->reveal(), $name, $bucket, null, $meta);
    }
}
