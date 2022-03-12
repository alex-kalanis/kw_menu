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
    public function testProcess(): void
    {
        $lib = $this->getLib();
        $this->assertFalse($lib->exists('none'));
        $this->assertTrue($lib->exists('target.meta'));
        $content = $lib->load('target.meta');
        $this->assertNotEmpty($content);
        $this->assertTrue($lib->save('target.meta', $content));
    }

    /**
     * @throws MenuException
     */
    public function testCannotRead(): void
    {
        $lib = $this->getLib();
        chmod(DirKey::getTarget() . 'unable.meta', 0222);
        $this->expectException(MenuException::class);
        $lib->load('unable.meta');
    }

    /**
     * @throws MenuException
     */
    public function testCannotWrite(): void
    {
        $lib = $this->getLib();
        chmod(DirKey::getTarget() . 'unable.meta', 0444);
        $this->assertFalse($lib->save('unable.meta', 'poiuztrewq'));
    }

    /**
     * @throws MenuException
     */
    public function testGetFiles(): void
    {
        $lib = $this->getLib();
        $iter = $lib->getFiles('');
        $files = iterator_to_array($iter);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));
    }

    protected function getLib()
    {
        DirKey::setDir($this->getTargetPath());
        return new EntriesSource\Storage(new XStorage(new Volume(), new Raw(), new DirKey()));
    }
}


class DirKey extends Key\DirKey
{
    public static function getTarget(): string
    {
        return static::$dir;
    }
}
