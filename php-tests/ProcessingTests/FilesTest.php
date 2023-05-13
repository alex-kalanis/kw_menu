<?php

namespace ProcessingTests;


use kalanis\kw_files\FilesException;
use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MetaProcessor;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\MoreEntries;
use kalanis\kw_paths\PathsException;


class FilesTest extends \CommonTestClass
{
    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testExisting(): void
    {
        // in meta source the path targets the meta file with records of entries
        // in entries source the path targets the directory with files or or group identifier of entries
        $path = $this->getTargetPath();
        $lib = $this->getLib($path); // meta with data
        $lib->setMeta(['target.meta']); // file with meta data
        $lib->load();
        $this->assertNotEmpty($lib->getMeta());
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testNew(): void
    {
        $path = $this->getTargetPath();
        $lib = $this->getLib($path); // meta with data
        $lib->setGroupKey(['dummy3']); // dir with data
        $lib->setMeta(['copy.meta']); // file with meta data
        $lib->load();
        $this->assertNotEmpty($lib->getMeta());
    }

    /**
     * @param mixed $params
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @return MoreEntries
     */
    protected function getLib($params): MoreEntries
    {
        return new MoreEntries(
            new MetaProcessor(
                (new MetaSource\Factory())->getSource($params)
            ),
            (new EntriesSource\Factory())->getSource($params)
        );
    }
}
