<?php

namespace ProcessingTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_paths\PathsException;


class FilesTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     * @throws PathsException
     */
    public function testExisting(): void
    {
        // in meta source the path targets the meta file with records of entries
        // in entries source the path targets the directory with files or or group identifier of entries
        $path = $this->getTargetPath();
        $lib = new MoreEntries(new MetaProcessor(new MetaSource\Volume($path, new MetaSource\FileParser())), new EntriesSource\Volume($path)); // meta with data
        $lib->setMeta('target.meta'); // dir with data
        $lib->load();
        $this->assertNotEmpty($lib->getMeta());
    }

    /**
     * @throws MenuException
     * @throws PathsException
     */
    public function testNew(): void
    {
        $path = $this->getTargetPath();
        $lib = new MoreEntries(new MetaProcessor(new MetaSource\Volume($path, new MetaSource\FileParser())), new EntriesSource\Volume($path)); // meta with data
        $lib->setGroupKey(['dummy3']); // dir with data
        $lib->setMeta('copy.meta'); // dir with data
        $lib->load();
        $this->assertNotEmpty($lib->getMeta());
    }
}
