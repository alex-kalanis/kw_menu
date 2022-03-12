<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_storage\Storage\Format\Raw;
use kalanis\kw_storage\Storage\Key;
use kalanis\kw_storage\Storage\Storage as XStorage;
use kalanis\kw_storage\Storage\Target\Volume;


class StorageTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testGetFiles(): void
    {
        Key\DirKey::setDir($this->getTargetPath());
        $lib = new EntriesSource\Storage(new XStorage(new Volume(), new Raw(), new Key\DirKey()));
        $iter = $lib->getFiles('');
        $files = iterator_to_array($iter);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));
    }
}
