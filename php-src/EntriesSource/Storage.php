<?php

namespace kalanis\kw_menu\DataSource;


use kalanis\kw_menu\Interfaces\IDataSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_menu\DataSource
 * Data source is in passed storage
 */
class Storage implements IDataSource
{
    use TFilterHtml;

    /** @var XStorage */
    protected $storage = null;

    public function __construct(XStorage $storage)
    {
        $this->storage = $storage;
    }

    public function exists(string $metaFile): bool
    {
        return $this->storage->exists($metaFile);
    }

    public function load(string $metaFile): string
    {
        try {
            return $this->storage->read($metaFile);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function save(string $metaFile, string $content): bool
    {
        try {
            return $this->storage->write($metaFile, $content);
            // @codeCoverageIgnoreStart
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getFiles(string $dir): Traversable
    {
        try {
            foreach ($this->storage->lookup($dir) as $item) {
                if ($this->filterExt(Stuff::fileExt($item))) {
                    yield $item;
                }
            }
            // @codeCoverageIgnoreStart
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }
}
