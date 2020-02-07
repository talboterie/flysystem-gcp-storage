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
}
