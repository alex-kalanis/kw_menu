<?php

namespace BasicTests;


use kalanis\kw_menu\Menu\Entry;
use kalanis\kw_menu\Menu\Menu;


class MenuEntriesTest extends \CommonTestClass
{
    /**
     * @param string $name
     * @param string $desc
     * @param string $id
     * @param int $position
     * @param bool $sub
     * @dataProvider contentProvider
     */
    public function testItem(string $name, string $desc, string $id, int $position, bool $sub): void
    {
        $lib = new Entry();
        $lib->setData($id, $name, $desc, $position, $sub);
        $this->assertEquals($name, $lib->getName());
        $this->assertEquals($desc, $lib->getDesc());
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
        $this->assertEmpty($lib->getEntries());
        foreach ($this->contentProvider() as list($name, $desc, $id, $position, $sub)) {
            $entry = new Entry();
            $entry->setData($id, $name, $desc, $position, $sub);
            $lib->addItem($entry);
        }
        $this->assertNotEmpty($lib->getEntries());
    }

    public function testSubMenu(): void
    {
        $entry = new Entry();
        $entry->setData('jkl', 'mno', 'pqr', 3, true);

        $this->assertEquals(3, $entry->getPosition());
        $entry->setPosition(1);
        $this->assertEquals(1, $entry->getPosition());
        $this->assertEmpty($entry->getSubmenu());

        $lib = new Menu();
        $lib->setData('abc', 'def', 'ghi', 11);
        $entry->addSubmenu($lib);
        $this->assertNotEmpty($entry->getSubmenu());
    }

    public function contentProvider(): array
    {
        return [
            ['abc', 'def', 'ghi', 2, false],
            ['jkl', 'mno', 'pqr', 3, true],
        ];
    }
}
