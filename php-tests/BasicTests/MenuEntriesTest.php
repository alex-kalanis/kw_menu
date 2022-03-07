<?php

namespace BasicTests;


use kalanis\kw_menu\Menu\Item;
use kalanis\kw_menu\Menu\Menu;


class MenuEntriesTest extends \CommonTestClass
{
    /**
     * @param string $name
     * @param string $title
     * @param string $file
     * @param int $position
     * @param bool $sub
     * @dataProvider contentProvider
     */
    public function testItem(string $name, string $title, string $file, int $position, bool $sub): void
    {
        $lib = new Item();
        $lib->setData($name, $title, $file, $position, $sub);
        $this->assertEquals($name, $lib->getName());
        $this->assertEquals($title, $lib->getTitle());
        $this->assertEquals($position, $lib->getPosition());
        $this->assertEquals($sub, $lib->canGoSub());
    }

    /**
     * @param string $file
     * @param string $name
     * @param string $title
     * @param int $counter
     * @param bool $unused
     * @dataProvider contentProvider
     */
    public function testHeader(string $file, string $name, string $title, int $counter, bool $unused): void
    {
        $lib = new Menu();
        $lib->setData($file, $name, $title, $counter);
        $this->assertEquals($file, $lib->getFile());
        $this->assertEquals($name, $lib->getName());
        $this->assertEquals($title, $lib->getTitle());
        $this->assertEquals($counter, $lib->getDisplayCount());
        $lib->clear();
    }

    public function testHeaderItems(): void
    {
        $lib = new Menu();
        $lib->setData('abc', 'def', 'ghi', 11);
        $this->assertEmpty($lib->getItems());
        foreach ($this->contentProvider() as list($name, $title, $file, $position, $sub)) {
            $item = new Item();
            $item->setData($name, $title, $file, $position, $sub);
            $lib->addItem($item);
        }
        $this->assertNotEmpty($lib->getItems());
    }

    public function testSubMenu(): void
    {
        $item = new Item();
        $item->setData('jkl', 'mno', 'pqr', 3, true);

        $this->assertEquals(3, $item->getPosition());
        $item->setPosition(1);
        $this->assertEquals(1, $item->getPosition());
        $this->assertEmpty($item->getSubmenu());

        $lib = new Menu();
        $lib->setData('abc', 'def', 'ghi', 11);
        $item->addSubmenu($lib);
        $this->assertNotEmpty($item->getSubmenu());
    }

    public function contentProvider(): array
    {
        return [
            ['abc', 'def', 'ghi', 2, false],
            ['jkl', 'mno', 'pqr', 3, true],
        ];
    }
}
