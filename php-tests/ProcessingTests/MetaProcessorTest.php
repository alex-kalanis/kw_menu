<?php

namespace ProcessingTests;


use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MetaSource\Volume;
use kalanis\kw_menu\MenuException;


class MetaProcessorTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testMenu(): void
    {
        $lib = $this->getLib();
        $lib->setKey('target.meta');
        $this->assertTrue($lib->exists());
        $this->assertNotEmpty($lib->getMenu());
        $this->assertEmpty($lib->getWorking());
        $lib->load();
        $this->assertNotEmpty($lib->getWorking());
        $lib->setKey('copy.meta');
        $lib->updateInfo('another target', 'another testing', null); // metadata info
        $lib->addEntry('other1.htm', 'ijn', 'uhb', true);
        $lib->updateEntry('dummy3.htm', 'edc', 'rfv', false); // entry info
        $lib->updateEntry('dummy4.htm', null, 'more lines' . PHP_EOL . 'than | it' . "\r\n" . 'worth', false); // entry info
        $lib->removeEntry('dummy2.htm');
        $lib->clearData();
        $lib->save();
    }

    /**
     * @throws MenuException
     */
    public function testPositions(): void
    {
        $lib = $this->getLib();
        $lib->setKey('target.meta');
        $this->assertTrue($lib->exists());
        $lib->load();
        $lib->setKey('copy.meta');
        $lib->rearrangePositions([ // file => new position
            'dummy4.htm' => 3,
            'dummy2.htm' => 4,
        ]);
        $lib->clearData();
        $lib->save();
    }

    /**
     * @throws MenuException
     */
    public function testNotEntry(): void
    {
        $lib = $this->getLib();
        $lib->setKey('target.meta');
        $this->assertTrue($lib->exists());
        $lib->load();
        $this->expectException(MenuException::class);
        $lib->updateEntry('not-a-file', 'no-save', 'nope', false);
    }

    /**
     * Try to rearrange empty settings
     * @throws MenuException
     */
    public function testNotPositions(): void
    {
        $lib = $this->getLib();
        $lib->setKey('target.meta');
        $this->assertTrue($lib->exists());
        $lib->load();
        $this->expectException(MenuException::class);
        $lib->rearrangePositions([]);
    }

    /**
     * Try to rearrange meta file which has not been loaded here
     * @throws MenuException
     */
    public function testNotPositionsItems(): void
    {
        $lib = $this->getLib();
        $lib->setKey('not-a-file');
        $this->assertFalse($lib->exists());
        $this->expectException(MenuException::class);
        $lib->rearrangePositions([1 => 3, 2 => 4]);
    }

    /**
     * Try to rearrange meta file which has set string, not number
     * @throws MenuException
     */
    public function testNotPositionsData(): void
    {
        $lib = $this->getLib();
        $lib->setKey('target.meta');
        $this->assertTrue($lib->exists());
        $lib->load();
        $this->expectException(MenuException::class);
        $lib->rearrangePositions([
            'dummy4.htm' => 'fail-there',
        ]);
    }

    protected function getLib()
    {
        return new MetaProcessor(new MetaSource\Volume($this->getTargetPath(), new MetaSource\FileParser()));
    }
}
