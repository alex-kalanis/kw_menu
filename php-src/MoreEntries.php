<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMNTranslations;


/**
 * Class MoreFiles
 * @package kalanis\kw_menu
 * Process the menu against the file tree
 * Load more already unloaded files and remove non-existing ones
 */
class MoreFiles
{
    /** @var string */
    protected $groupKey = '';
    /** @var MetaProcessor */
    protected $data = null;
    /** @var Interfaces\IEntriesSource */
    protected $dataSource = null;

    public function __construct(Interfaces\IMetaSource $metaSource, Interfaces\IEntriesSource $dataSource, ?IMNTranslations $lang = null)
    {
        $this->data = new MetaProcessor($metaSource, $lang);
        $this->dataSource = $dataSource;
    }

    /**
     * @param string $groupKey directory
     * @return $this
     */
    public function setGroup(string $groupKey): self
    {
        $this->groupKey = $groupKey;
        $this->data->setKey($groupKey);
        return $this;
    }

    /**
     * @return $this
     * @throws MenuException
     */
    public function load(): self
    {
        if ($this->data->exists()) {
            $this->data->load();
            $this->fillMissing();
        } else {
            $this->createNew();
        }
        return $this;
    }

    /**
     * @throws MenuException
     */
    protected function createNew(): void
    {
        foreach ($this->dataSource->getFiles($this->groupKey) as $file) {
            $this->data->addEntry($file);
        }
    }

    /**
     * @throws MenuException
     */
    protected function fillMissing(): void
    {
        $toRemoval = array_map([$this, 'fileName'], $this->data->getWorking());
        $toRemoval = array_combine($toRemoval, array_fill(0, count($toRemoval), true));

        foreach ($this->dataSource->getFiles($this->groupKey) as $file) {
            $alreadyKnown = false;
            foreach ($this->data->getWorking() as $item) { # stay
                if ((!$alreadyKnown) && ($item->getFile() == $file)) {
                    $alreadyKnown = true;
                    $toRemoval[$item->getFile()] = false;
                }
            }
            if (!$alreadyKnown) {
                $this->data->addEntry($file);
            }
        }
        foreach ($this->data->getWorking() as $item) {
            if (!empty($toRemoval[$item->getFile()])) {
                $this->data->removeEntry($item->getFile());
            }
        }
    }

    public function fileName(Menu\Item $item): string
    {
        return $item->getFile();
    }

    public function getData(): MetaProcessor
    {
        return $this->data;
    }
}
