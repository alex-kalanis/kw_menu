<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\Interfaces\IMetaFileParser;
use kalanis\kw_menu\Interfaces\IMetaSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_paths\PathsException;


/**
 * Class Files
 * @package kalanis\kw_menu\MetaSource
 * Data source is in passed Files package
 */
class Files implements IMetaSource
{
    use TLang;

    /** @var string[] */
    protected $key = [];
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var IMetaFileParser */
    protected $parser = null;

    /**
     * @param CompositeAdapter $files
     * @param IMetaFileParser $parser
     * @param IMNTranslations|null $lang
     * @param string[] $metaKey
     */
    public function __construct(CompositeAdapter $files, IMetaFileParser $parser, ?IMNTranslations $lang = null, array $metaKey = [])
    {
        $this->setMnLang($lang);
        $this->files = $files;
        $this->parser = $parser;
        $this->key = $metaKey;
    }

    public function setSource(array $metaPath): void
    {
        $this->key = $metaPath;
    }

    public function exists(): bool
    {
        try {
            return $this->files->exists($this->key);
        } catch (FilesException | PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(): Menu
    {
        try {
            return $this->parser->unpack($this->toString($this->files->readFile($this->key)));
        } catch (FilesException | PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string|resource $content
     * @throws MenuException
     * @return string
     */
    protected function toString($content): string
    {
        if (is_resource($content)) {
            rewind($content);
            $data = stream_get_contents($content, -1, 0);
            if (false === $data) {
                // @codeCoverageIgnoreStart
                // must die something with stream reading
                throw new MenuException($this->getMnLang()->mnCannotOpen());
            }
            // @codeCoverageIgnoreEnd
            return strval($data);
        } else {
            return strval($content);
        }
    }

    public function save(Menu $content): bool
    {
        try {
            return $this->files->saveFile($this->key, $this->parser->pack($content));
        } catch (FilesException | PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
