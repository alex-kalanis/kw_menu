<?php

namespace MetaSourceTests;


use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces as files_interfaces;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces as storages_interfaces;
use kalanis\kw_storage\Storage\Key;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\Storage\Target;
use kalanis\kw_storage\StorageException;


class FilesTest extends \CommonTestClass
{
    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testProcess(): void
    {
        $lib = $this->getLib();
        $lib->setSource(['none']);
        $this->assertFalse($lib->exists());
        $lib->setSource(['target.meta']);
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
        $lib->setSource(['copy.meta']);
        $this->assertTrue($lib->save($content));
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws StorageException
     */
    public function testProcessStream1(): void
    {
        $lib = $this->getStreamLib();
        $lib->setSource(['stream1.meta']);
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws StorageException
     */
    public function testProcessStream2(): void
    {
        $lib = $this->getStreamLib();
        $lib->setSource(['stream2.meta']);
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws StorageException
     */
    public function testProcessStream3(): void
    {
        $lib = $this->getStreamLib();
        $lib->setSource(['stream3.meta']);
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testFailedMeta(): void
    {
        $lib = $this->getFailLib();
        $this->expectException(MenuException::class);
        $lib->exists();
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testFailedLoad(): void
    {
        $lib = $this->getFailLib();
        $this->expectException(MenuException::class);
        $lib->load();
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testFailedSave(): void
    {
        $lib = $this->getFailLib();
        $this->expectException(MenuException::class);
        $lib->save(new Menu());
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return MetaSource\Files
     */
    protected function getLib(): MetaSource\Files
    {
        return new MetaSource\Files((new Factory())->getClass($this->getTargetPath()), new MetaSource\FileParser(), null, ['target.meta']);
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return MetaSource\Files
     */
    protected function getFailLib(): MetaSource\Files
    {
        return new MetaSource\Files((new Factory())->getClass(new \XFailStorage(new Key\DefaultKey(), new Target\Memory())), new MetaSource\FileParser(), null, ['target.meta']);
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     * @return MetaSource\Files
     */
    protected function getStreamLib(): MetaSource\Files
    {
        Key\DirKey::setDir('');
        return new MetaSource\Files((new Factory())->getClass(new Storage(new Key\DirKey(), $this->filledMemory())), new MetaSource\FileParser());
    }

    /**
     * @throws StorageException
     * Beware - needs root node!
     * @return storages_interfaces\ITarget
     */
    protected function filledMemory(): storages_interfaces\ITarget
    {
        $stream1 = fopen('php://memory', 'r+');
        fwrite($stream1,'Just for unable read');
        $stream2 = fopen('php://memory', 'r+');
        fwrite($stream2, implode("\r\n", [
            'dummy|0|not use|do not use|',
            '',
            '# skip line',
            'local.htm|1|local file|not shown|0|',
        ]));
        $stream3 = fopen('php://memory', 'r+');
        fwrite($stream3,'Just for unable read' . "\r\n");
        $lib = new Target\Memory();
        $lib->save('', files_interfaces\IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'stream1.meta', $stream1);
        $lib->save(DIRECTORY_SEPARATOR . 'stream2.meta', $stream2);
        $lib->save(DIRECTORY_SEPARATOR . 'stream3.meta', $stream3);
        return $lib;
    }
}
