<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Path;
use kalanis\kw_tree as XTree;
use SplFileInfo;
use Traversable;


/**
 * Class Tree
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in passed tree
 */
class Tree implements IEntriesSource
{
    use TFilterHtml;

    /** @var XTree\DataSources\Volume */
    protected $tree = null;
    /** @var ArrayPath */
    protected $arrPath = null;

    public function __construct(Path $path)
    {
        $this->tree = new XTree\DataSources\Volume($path->getDocumentRoot() . $path->getPathToSystemRoot());
        $this->arrPath = new ArrayPath();
    }

    public function getFiles(array $path): Traversable
    {
        $this->tree->setStartPath($path);
        $this->tree->wantDeep(false);
        $this->tree->setFilterCallback([$this, 'filterHtml']);
        $this->tree->process();
        foreach ($this->tree->getRoot()->getSubNodes() as $item) {
            yield $this->arrPath->setArray($item->getPath())->getFileName();
        }
    }

    public function filterHtml(SplFileInfo $info): bool
    {
        return $this->filterExt($info->getExtension());
    }
}
