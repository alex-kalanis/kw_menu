<?php

namespace ProcessingTests;


use kalanis\kw_menu\DataSource\Volume;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreFiles;


class FilesTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testExisting(): void
    {
        // in volume it's document root
        // in path then the currently opened path
        // name of metafile must stay the same across the project
        $lib = new MoreFiles(new Volume($this->getTargetPath()), 'target.meta'); // meta with data
        $lib->setPath(''); // dir with data
        $lib->load();
        $this->assertNotEmpty($lib->getData());
    }

    /**
     * @throws MenuException
     */
    public function testNew(): void
    {
        $lib = new MoreFiles(new Volume($this->getTargetPath()), 'copy.meta'); // meta with data
        $lib->setPath('dummy3'); // dir with data
        $lib->load();
        $this->assertNotEmpty($lib->getData());
    }
}
