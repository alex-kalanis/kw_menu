<?php

namespace kalanis\kw_menu\DataSource;


use kalanis\kw_menu\Interfaces\IDataSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Path;
use kalanis\kw_storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;
use kalanis\kw_tree\Tree;
use SplFileInfo;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_menu\DataSource
 * Data source is in passed storage
 *
 * @todo: this is not what I fully want - storage and tree are 2 different sources, not just one;
 *        but it's better than have it on one heap with normal files, that shall be a problem
 */
class Storage implements IDataSource
{
    /** @var Tree */
    protected $tree = null;
    /** @var XStorage */
    protected $storage = null;

    public function __construct(Path $path, XStorage $storage)
    {
        $this->tree = new Tree($path);
        $this->storage = $storage;
    }

    public function exists(string $metaFile): bool
    {
        try {
            return $this->storage->exists($metaFile);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(string $metaFile): string
    {
        try {
            return $this->storage->get($metaFile);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function save(string $metaFile, string $content): bool
    {
        try {
            return $this->storage->set($metaFile, $content);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function getFiles(string $dir): Traversable
    {
        $this->tree->startFromPath($dir);
        $this->tree->canRecursive(false);
        $this->tree->setFilterCallback([$this, 'filterHtml']);
        $this->tree->process();
        foreach ($this->tree->getTree()->getSubNodes() as $item) {
            yield $item->getName();
        }
    }

    public function filterHtml(SplFileInfo $info): bool
    {
        return in_array($info->getExtension(), ['htm', 'html', 'xhtm', 'xhtml']);
    }
}
