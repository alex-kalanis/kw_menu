<?php

namespace EntriesSourceTests;


use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces as files_interfaces;
use kalanis\kw_menu\EntriesSource;
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
    public function testGetFiles(): void
    {
        $lib = $this->getLibVolume();
        $iter = $lib->getFiles([]);
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));

        $iter2 = $lib->getFiles(['dummy3']);
        $files2 = iterator_to_array($iter2);
        $this->assertFalse(in_array('dummy4.htm', $files2));
        $this->assertTrue(in_array('dummy5.htm', $files2));
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws StorageException
     */
    public function testGetStorage(): void
    {
        $lib = $this->getLibStorage();
        $iter = $lib->getFiles([]);
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));

        $iter2 = $lib->getFiles(['dummy3']);
        $files2 = iterator_to_array($iter2);
        $this->assertFalse(in_array('dummy4.htm', $files2));
        $this->assertTrue(in_array('dummy5.htm', $files2));
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @throws StorageException
     */
    public function testFailedMeta(): void
    {
        $lib = $this->getLibStorageFail();
        $this->expectException(MenuException::class);
        iterator_to_array($lib->getFiles([]));
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return EntriesSource\Files
     */
    protected function getLibVolume(): EntriesSource\Files
    {
        return new EntriesSource\Files((new Factory())->getClass($this->getTargetPath()));
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     * @return EntriesSource\Files
     */
    protected function getLibStorage(): EntriesSource\Files
    {
        Key\DirKey::setDir('');
        return new EntriesSource\Files(
            (new Factory())->getClass(new Storage(new Key\DirKey(), $this->filledMemory()))
        );
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     * @return EntriesSource\Files
     */
    protected function getLibStorageFail(): EntriesSource\Files
    {
        Key\DirKey::setDir('');
        return new EntriesSource\Files(
            (new Factory())->getClass(new XFailStorage(new Key\DirKey(), new Target\Memory()))
        );
    }

    /**
     * @return storages_interfaces\ITarget
     * @throws StorageException
     * Beware - needs root node!
     */
    protected function filledMemory(): storages_interfaces\ITarget
    {
        $lib = new Target\Memory();
        $lib->save('', files_interfaces\IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'dummy3', files_interfaces\IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'dummy3' . DIRECTORY_SEPARATOR . 'dummy5.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'dummy3' . DIRECTORY_SEPARATOR . 'dummy6.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'dummy1.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'dummy2.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'dummy3.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'dummy4.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'other1.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'other2.htm', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        return $lib;
    }
}


class XFailStorage extends Storage
{
    public function exists(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }
}
