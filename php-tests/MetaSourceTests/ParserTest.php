<?php

namespace MetaSourceTests;


use kalanis\kw_menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MetaSource;


class ParserTest extends \CommonTestClass
{
    public function testUnpackOk(): void
    {
        $lib = new MetaSource\FileParser();
        $result = $lib->unpack($this->okFile());
        $this->assertEquals('target', $result->getFile());
        $this->assertEquals('test target', $result->getName());
        $this->assertEquals('testing', $result->getTitle());
        $this->assertEquals(4, $result->getDisplayCount());

        $entries = $result->getEntries();
        $entry = reset($entries);
        $this->assertEquals('dummy1.htm', $entry->getId());
        $this->assertEquals('abc', $entry->getName());
        $this->assertEquals('def', $entry->getDesc());
        $this->assertEquals(1, $entry->getPosition());
        $this->assertFalse($entry->canGoSub());
        $this->assertEmpty($entry->getSubmenu());

        $entry = next($entries);
        $this->assertEquals('dummy3.htm', $entry->getId());
        $this->assertEquals('mno', $entry->getName());
        $this->assertEquals('pqr', $entry->getDesc());
        $this->assertEquals(4, $entry->getPosition());
        $this->assertTrue($entry->canGoSub());
        $this->assertEmpty($entry->getSubmenu());

        $entry = next($entries);
        $this->assertEquals('dummy4.htm', $entry->getId());
        $this->assertEquals('stu', $entry->getName());
        $this->assertEquals('vyz', $entry->getDesc());
        $this->assertEquals(5, $entry->getPosition());
        $this->assertFalse($entry->canGoSub());
        $this->assertEmpty($entry->getSubmenu());

        $entry = next($entries);
        $this->assertEquals('unknown.htm', $entry->getId());
        $this->assertEquals('uhb', $entry->getName());
        $this->assertEquals('zgv', $entry->getDesc());
        $this->assertEquals(6, $entry->getPosition());
        $this->assertTrue($entry->canGoSub());
        $this->assertEmpty($entry->getSubmenu());

        $this->assertFalse(next($entries));
    }

    /**
     * @throws MenuException
     */
    public function testPack(): void
    {
        $menu = new Menu\Menu();
        $menu->setData('target', 'test target', 'testing', 4);

        $entry = new Menu\Entry();
        $entry->setData('dummy1.htm', 'abc', 'def', 1);
        $menu->addItem($entry);

        $entry = new Menu\Entry();
        $entry->setData('dummy3.htm', 'mno', 'pqr', 4, true);
        $menu->addItem($entry);

        $lib = new MetaSource\FileParser();
        $this->assertEquals($this->okResult(), $lib->pack($menu));
    }

    public function testEmptyFile(): void
    {
        $lib = new MetaSource\FileParser();
        $result = $lib->unpack($this->emptyFile());
        $this->assertEmpty($result->getTitle());
    }

    public function testEmptyWithoutContent(): void
    {
        $lib = new MetaSource\FileParser();
        $result = $lib->unpack($this->failDataFile());
        $this->assertEmpty($result->getTitle());
    }

    protected function okFile(): string
    {
        return implode("\r\n", [
            'target|4|test target|testing|',
            '',
            'dummy1.htm|1|abc|def|0|',
            '#',
            'problematic on many cases',
            'dummy3.htm|4|mno|pqr|1|',
            'dummy4.htm|5|stu|vyz|0|',
            'unknown.htm|6|uhb|zgv|1|',
            '',
        ]);
    }

    protected function okResult(): string
    {
        return implode("\r\n", [
            'target|4|test target|testing|',
            '#',
            'dummy1.htm|1|abc|def|0|',
            'dummy3.htm|4|mno|pqr|1|',
            '',
        ]);
    }

    protected function emptyFile(): string
    {
        return '';
    }

    protected function failDataFile(): string
    {
        return implode("\r\n", [
            '',
            '',
        ]);
    }
}
