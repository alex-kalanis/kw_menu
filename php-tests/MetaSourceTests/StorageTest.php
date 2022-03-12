<?php

namespace MetaSourceTests;


use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MetaSource;
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
        $lib->setSource('none');
        $this->assertFalse($lib->exists());
        $lib->setSource('target.meta');
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
        $lib->setSource('copy.meta');
        $this->assertTrue($lib->save($content));
    }

    /**
     * @throws MenuException
     */
    public function testCannotRead(): void
    {
        $lib = $this->getLib();
        chmod($this->getTargetPath() . 'unable.meta', 0222);
        $lib->setSource('unable.meta');
        $this->expectException(MenuException::class);
        $lib->load();
    }

    /**
     * @throws MenuException
     */
    public function testCannotWrite(): void
    {
        $lib = $this->getLib();
        chmod($this->getTargetPath() . 'unable.meta', 0444);
        $lib->setSource('unable.meta');
        $this->assertFalse($lib->save(new Menu()));
    }

    protected function getLib()
    {
        Key\DirKey::setDir($this->getTargetPath());
        return new MetaSource\Storage(new XStorage(new Volume(), new Raw(), new Key\DirKey()), new MetaSource\FileParser(), 'target.meta');
    }
}
