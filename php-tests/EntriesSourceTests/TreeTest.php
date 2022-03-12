<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\DataSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Path;


class TreeTest extends \CommonTestClass
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
        chmod($lib->getTarget() . 'unable.meta', 0222);
        $this->expectException(MenuException::class);
        $lib->load('unable.meta');
    }

    /**
     * @throws MenuException
     */
    public function testCannotWrite(): void
    {
        $lib = $this->getLib();
        $this->expectException(MenuException::class);
        $lib->save('dummy3', 'poiuztrewq');
    }

    /**
     * @throws MenuException
     */
    public function testGetFiles(): void
    {
        $lib = $this->getLib();
        $iter = $lib->getFiles('');
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));
    }

    protected function getLib()
    {
        $path = new Path();
        $path->setDocumentRoot($this->getTargetPath());
        return new Tree($path);
    }
}


class Tree extends DataSource\Tree
{
    public function getTarget(): string
    {
        return $this->rootPath;
    }
}
