<?php

namespace kalanis\kw_menu\DataSource;


use kalanis\kw_menu\Interfaces\IDataSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Translations;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\Tree as XTree;
use SplFileInfo;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_menu\DataSource
 * Data source is in passed storage
 */
class Tree implements IDataSource
{
    use TFilterHtml;

    /** @var string path to menu dir */
    protected $rootPath = '';
    /** @var XTree */
    protected $tree = null;
    /** @var IMNTranslations */
    protected $lang = null;

    public function __construct(Path $path, ?IMNTranslations $lang = null)
    {
        $this->tree = new XTree($path);
        $this->rootPath = Stuff::removeEndingSlash($path->getDocumentRoot() . $path->getPathToSystemRoot()) . DIRECTORY_SEPARATOR;
        $this->lang = $lang ?: new Translations();
    }

    public function exists(string $metaFile): bool
    {
        return is_file($this->rootPath . $metaFile);
    }

    public function load(string $metaFile): string
    {
        $content = @file_get_contents($this->rootPath . $metaFile);
        if (false === $content) {
            throw new MenuException($this->lang->mnCannotOpen());
        }
        return $content;
    }

    public function save(string $metaFile, string $content): bool
    {
        if (false === @file_put_contents($this->rootPath . $metaFile, $content)) {
            throw new MenuException($this->lang->mnCannotSave());
        }
        return true;
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
        return $this->filterExt($info->getExtension());
    }
}
