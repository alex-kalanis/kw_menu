<?php

use PHPUnit\Framework\TestCase;


/**
 * Class CommonTestClass
 * The structure for mocking and configuration seems so complicated, but it's necessary to let it be totally idiot-proof
 */
class CommonTestClass extends TestCase
{
    protected function tearDown(): void
    {
        $ptXM = $this->getTargetPath() . 'copy.meta';
        if (is_file($ptXM)) {
            @unlink($ptXM);
        }
    }

    protected function getTargetPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR;
    }

    protected function getTargetPath2(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'target';
    }
}


class XFailStorage extends \kalanis\kw_storage\Storage\Storage
{
    public function write(string $sharedKey, $data, ?int $timeout = null): bool
    {
        throw new \kalanis\kw_storage\StorageException('mock');
    }

    public function read(string $sharedKey)
    {
        throw new \kalanis\kw_storage\StorageException('mock');
    }

    public function exists(string $sharedKey): bool
    {
        throw new \kalanis\kw_storage\StorageException('mock');
    }

    public function lookup(string $mask): Traversable
    {
        throw new \kalanis\kw_storage\StorageException('mock');
    }
}
