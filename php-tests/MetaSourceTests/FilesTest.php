<?php

namespace MetaSourceTests;


use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage\Key;
use kalanis\kw_storage\Storage\Target;


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
        return new MetaSource\Files((new Factory())->getClass($this->getTargetPath()), new MetaSource\FileParser(), ['target.meta']);
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return MetaSource\Files
     */
    protected function getFailLib(): MetaSource\Files
    {
        return new MetaSource\Files((new Factory())->getClass(new \XFailStorage(new Key\DefaultKey(), new Target\Memory())), new MetaSource\FileParser(), ['target.meta']);
    }
}
