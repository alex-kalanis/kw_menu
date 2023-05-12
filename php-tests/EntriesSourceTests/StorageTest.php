<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage\Key;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\Storage\Target;


class StorageTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     * @throws PathsException
     */
    public function testGetFiles(): void
    {
        $lib = $this->getLib();
        $iter = $lib->getFiles([]);
        $files = iterator_to_array($iter);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));

        $iter2 = $lib->getFiles(['dummy3']);
        $files2 = iterator_to_array($iter2);
        $this->assertFalse(in_array('dummy4.htm', $files2));
        $this->assertTrue(in_array('dummy5.htm', $files2));
    }

    /**
     * @throws MenuException
     * @throws PathsException
     */
    public function testFailedLookup(): void
    {
        $lib = $this->getFailLib();
        $this->expectException(MenuException::class);
        iterator_to_array($lib->getFiles([]));
    }

    protected function getLib(): EntriesSource\Storage
    {
        Key\DirKey::setDir($this->getTargetPath());
        return new EntriesSource\Storage(new Storage(new Key\DirKey(), new Target\Volume()));
    }

    protected function getFailLib(): EntriesSource\Storage
    {
        return new EntriesSource\Storage(new \XFailStorage(new Key\DefaultKey(), new Target\Memory()));
    }
}
