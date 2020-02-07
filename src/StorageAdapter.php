<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage;

use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageObject;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use LogicException;

class StorageAdapter extends AbstractAdapter
{
    /** @var Bucket */
    protected $bucket;

    public function __construct(Bucket $bucket, string $prefix = '')
    {
        $this->bucket = $bucket;
        $this->setPathPrefix($prefix);
    }

    public function copy($path, $newPath): StorageObject
    {
        return $this->move($path, $newPath, false);
    }

    public function createDir($dirname, Config $config)
    {
        throw new LogicException(get_class($this) . ' create directory as needed when writing file. Path: ' . $dirname);
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
        /** @var StorageObject[] $objects */
        $objects = $this->bucket->objects(['prefix' => $dirname . '/']);

        foreach ($objects as $object) {
            $object->delete();
        }

        return true;
    }

    public function getMetadata($path)
    {
        return $this->bucket->object($this->applyPathPrefix($path))->info();
    }

    public function getMimetype($path)
    {
        $meta = $this->getMetadata($path);

        return ['mimetype' => $meta['contentType']];
    }

    public function getSize($path)
    {
        $meta = $this->getMetadata($path);

        return ['size' => $meta['size']];
    }

    public function getTimestamp($path)
    {
        $meta = $this->getMetadata($path);

        return ['timestamp' => strtotime($meta['updated'])];
    }

    public function getVisibility($path)
    {
        $visibility = $this->bucket->object($this->applyPathPrefix($path))->acl()->get();

        return compact('path', 'visibility');
    }

    public function has($path)
    {
        return $this->bucket->object($this->applyPathPrefix($path))->exists();
    }

    public function listContents($directory = '', $recursive = false)
    {
        $options = [];
        if (!empty($directory)) {
            $options['prefix'] = $directory . '/';
        }

        /** @var StorageObject[] $objects */
        $objects = $this->bucket->objects($options);

        $items = [];
        foreach ($objects as $object) {
            $items[] = ['path' => $object->name()];
        }

        return $items;
    }

    public function read($path)
    {
        $contents = $this->bucket->object($this->applyPathPrefix($path))->downloadAsString();

        return compact('contents');
    }

    public function readStream($path)
    {
        $stream = $this->bucket->object($this->applyPathPrefix($path))->downloadAsStream();

        return compact('stream');
    }

    public function rename($path, $newPath): StorageObject
    {
        return $this->move($path, $newPath);
    }

    public function setVisibility($path, $visibility)
    {
        $object = $this->bucket->object($this->applyPathPrefix($path));
        $object->update(['acl' => []], ['predefinedAcl' => $visibility]);

        return compact('path', 'visibility');
    }

    public function update($path, $contents, Config $config): StorageObject
    {
        return $this->upload($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config): StorageObject
    {
        return $this->upload($path, $resource, $config);
    }

    public function write($path, $contents, Config $config): StorageObject
    {
        return $this->upload($path, $contents, $config);
    }

    public function writeStream($path, $resource, Config $config): StorageObject
    {
        return $this->upload($path, $resource, $config);
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

    protected function upload(string $path, $content, Config $config): StorageObject
    {
        return $this->bucket->upload($content, ['name' => $this->applyPathPrefix($path)]);
    }
}
