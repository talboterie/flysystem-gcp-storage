<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage;

use League\Flysystem\Config;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageObject;
use League\Flysystem\Adapter\AbstractAdapter;
use Google\Cloud\Core\Exception\NotFoundException;

class StorageAdapter extends AbstractAdapter
{
    /** @var Bucket */
    protected $bucket;

    public function __construct(Bucket $bucket, string $prefix = '')
    {
        $this->bucket = $bucket;
        $this->setPathPrefix($prefix);
    }

    protected function upload(string $path, $content, Config $config): StorageObject
    {
        return $this->bucket->upload($content, ['name' => $this->applyPathPrefix($path)]);
    }

    protected function move(string $path, string $newPath, bool $delete = true): StorageObject
    {
        $object = $this->bucket->object($this->applyPathPrefix($path));
        $newObject = $object->copy($this->bucket->name(), ['name' => $this->applyPathPrefix($newPath)]);

        if ($delete) {
            $object->delete();
        }

        return $newObject;
    }

    public function write($path, $contents, Config $config): StorageObject
    {
        return $this->upload($path, $contents, $config);
    }

    public function writeStream($path, $resource, Config $config): StorageObject
    {
        return $this->upload($path, $resource, $config);
    }

    public function update($path, $contents, Config $config): StorageObject
    {
        return $this->upload($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config): StorageObject
    {
        return $this->upload($path, $resource, $config);
    }

    public function rename($path, $newPath): StorageObject
    {
        return $this->move($path, $newPath);
    }

    public function copy($path, $newPath): StorageObject
    {
        return $this->move($path, $newPath, false);
    }

    public function delete($path): bool
    {
        try {
            $this->bucket->object($this->applyPathPrefix($path))->delete();

            return true;
        } catch (NotFoundException $exception) {
            return false;
        }
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    public function has($path)
    {
        // TODO: Implement has() method.
    }

    public function read($path)
    {
        // TODO: Implement read() method.
    }

    public function readStream($path)
    {
        // TODO: Implement readStream() method.
    }

    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
    }

    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }
}
