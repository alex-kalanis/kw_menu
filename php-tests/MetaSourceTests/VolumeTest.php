<?php

namespace MetaSourceTests;


use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;


class VolumeTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testProcess(): void
    {
        $lib = $this->getLib();
        $lib->setSource(['not-a-file']);
        $this->assertFalse($lib->exists());
        $lib->setSource(['target.meta']);
        $this->assertTrue($lib->exists());
        $content = $lib->load();
        $this->assertNotEmpty($content);
        $lib->setSource(['copy.meta']);
        $this->assertTrue($lib->save($content));
    }

    /**
     * @throws MenuException
     */
    public function testCannotRead(): void
    {
        $lib = $this->getLib();
        $lib->setSource(['unable.meta']);
        chmod($this->getTargetPath() . 'unable.meta', 0222);
        $this->expectException(MenuException::class);
        $lib->load();
    }

    /**
     * @throws MenuException
     */
    public function testCannotWrite(): void
    {
        $lib = $this->getLib();
        $lib->setSource(['unable.meta']);
        chmod($this->getTargetPath() . 'unable.meta', 0444);
        $this->expectException(MenuException::class);
        $lib->save(new Menu());
    }

    /**
     * @throws MenuException
     */
    public function testFailExists(): void
    {
        $lib = $this->getLibFailing();
        $lib->setSource(['target.meta']);
        $this->expectException(MenuException::class);
        $lib->exists();
    }

    /**
     * @throws MenuException
     */
    public function testFailLoad(): void
    {
        $lib = $this->getLibFailing();
        $lib->setSource(['target.meta']);
        $this->expectException(MenuException::class);
        $lib->load();
    }

    /**
     * @throws MenuException
     */
    public function testFailSave(): void
    {
        $lib = $this->getLibFailing();
        $lib->setSource(['target.meta']);
        $this->expectException(MenuException::class);
        $lib->save(new Menu());
    }

    protected function getLib(): MetaSource\Volume
    {
        return new MetaSource\Volume($this->getTargetPath(), new MetaSource\FileParser());
    }

    protected function getLibFailing(): MetaSource\Volume
    {
        return new XVolume($this->getTargetPath(), new MetaSource\FileParser());
    }
}


class XVolume extends MetaSource\Volume
{
    protected function systemDelimiter(): string
    {
        return '';
    }
}