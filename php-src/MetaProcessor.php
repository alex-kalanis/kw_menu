<?php

namespace kalanis\kw_menu;


use kalanis\kw_menu\Interfaces\IMNTranslations;


/**
 * Class DataProcessor
 * @package kalanis\kw_menu
 * Menu data processor - CRUD
 */
class DataProcessor
{
    /** @var Interfaces\IMetaSource|null */
    protected $metaSource = null;
    /** @var IMNTranslations */
    protected $lang = null;
    /** @var Menu\Menu */
    protected $menu = null;
    /** @var Menu\Item */
    protected $item = null;
    /** @var int */
    protected $highest = 0;
    /** @var Menu\Item[] */
    protected $workList = [];

    public function __construct(Interfaces\IMetaSource $metaSource, ?IMNTranslations $lang = null)
    {
        $this->menu = new Menu\Menu();
        $this->item = new Menu\Item();
        $this->metaSource = $metaSource;
        $this->lang = $lang ?: new Translations();
    }

    /**
     * @return bool
     * @throws MenuException
     */
    public function exists(): bool
    {
        return $this->metaSource->exists();
    }

    /**
     * @return Menu\Menu
     */
    public function getMenu(): Menu\Menu
    {
        return $this->menu;
    }

    /**
     * @throws MenuException
     */
    public function load(): void
    {
        if (empty($this->menu->getItems()) && empty($this->menu->getFile()) && $this->exists()) {
            $this->menu = $this->metaSource->load();
            $this->workList = $this->menu->getItems();
        }
    }

    public function updateInfo(?string $name, ?string $desc, ?int $displayCount): void
    {
        $this->menu->setData(
            $this->menu->getFile(),
            $name ?: $this->menu->getName(),
            $desc ?: $this->menu->getTitle(),
            $displayCount ?: $this->highest
        );
    }

    public function getItem(string $file): ?Menu\Item
    {
        foreach ($this->workList as &$item) {
            if ($item->getFile() == $file) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return Menu\Item[]
     */
    public function getWorking(): array
    {
        $this->sortItems();
        return $this->workList;
    }

    public function addEntry(string $file, string $name = '', string $desc = '', bool $sub = false): void
    {
        if (!$this->getItem($file)) {
            $name = empty($name) ? $file : $name;
            $item = clone $this->item;
            $this->highest++;
            $this->workList[$file] = $item->setData($name, $desc, $file, $this->highest, $sub);
        }
    }

    /**
     * @param string $file
     * @param string|null $name
     * @param string|null $desc
     * @param bool|null $sub
     * @throws MenuException
     */
    public function updateEntry(string $file, ?string $name, ?string $desc, ?bool $sub): void
    {
        # null sign means not free, just unchanged
        $item = $this->getItem($file);
        if (!$item) {
            throw new MenuException($this->lang->mnItemNotFound($file));
        }

        $item->setData(
            is_null($name) ? $item->getName() : $name,
            is_null($desc) ? $item->getTitle() : $desc,
            $file,
            $item->getPosition(),
            is_null($sub) ? $item->canGoSub() : $sub
        );
    }

    public function removeEntry(string $file): void
    {
        if ($item = $this->getItem($file)) {
            unset($this->workList[$item->getFile()]);
        }
    }

    /**
     * @param array<string, int> $positions
     * @throws MenuException
     */
    public function rearrangePositions(array $positions): void
    {
        # get assoc array with new positioning of files
        # key is file name, value is new position
        if (empty($positions)) {
            throw new MenuException($this->lang->mnProblematicData());
        }
        $matrix = [];
        # all at first
        foreach ($this->workList as &$item) {
            $matrix[$item->getPosition()] = $item->getPosition();
        }
        # updated at second
        foreach ($positions as $file => &$position) {
            if (empty($this->workList[$file])) {
                throw new MenuException($this->lang->mnItemNotFound($file));
            }
            if (!is_numeric($position)) {
                throw new MenuException($this->lang->mnProblematicData());
            }
            $matrix[$this->workList[$file]->getPosition()] = intval($position);
        }

        $prepared = [];
        foreach ($matrix as $old => &$new) {
            if ($old == $new) { # don't move, stay there
                $prepared[$new] = $old;
                unset($matrix[$old]);
            }
        }

        while (count($matrix) > 0) {
            foreach ($matrix as $old => &$new) {
                if (!isset($prepared[$new])) { # nothing on new position
                    $prepared[$new] = $old;
                    unset($matrix[$old]);
                } else {
                    $matrix[$old]++; # on next round try next position
                }
            }
        }

        $prepared = array_flip($prepared); # flip positions back, index is original one, not new one
        foreach ($this->workList as &$item) {
            $item->setPosition($prepared[$item->getPosition()]);
        }
    }

    public function clearData(): self
    {
        $this->highest = $this->getHighestKnownPosition();
        $this->clearHoles();
        $this->highest = $this->getHighestKnownPosition();
        return $this;
    }

    protected function clearHoles(): void
    {
        $max = $this->highest;
        $use = [];
        $hole = false;
        $j = 0;

        /** @var Menu\Item[] $workList */
        $workList = [];
        foreach ($this->workList as &$item) {
            $workList[$item->getPosition()] = $item->getFile(); # old position contains file ***
        }

        for ($i = 0; $i <= $max; $i++) {
            if (!empty($workList[$i])) { # position contains data
                $use[$j] = $workList[$i]; # new position contains file named *** from old one...
                $j++;
                $hole = false;
            } elseif (!$hole) { # first free position
                $j++;
                $hole = true;
            }
            # more than one free position
        }
        $use = array_flip($use); # flip back to names as PK

        foreach ($this->workList as &$item) {
            $item->setPosition($use[$item->getFile()]);
        }
    }

    protected function getHighestKnownPosition(): int
    {
        return (int)array_reduce($this->workList, [$this, 'maxPosition'], 0);
    }

    public function maxPosition($carry, Menu\Item $item)
    {
        return max($carry, $item->getPosition());
    }

    /**
     * @throws MenuException
     */
    public function save(): void
    {
        $this->menu->setData( // reset entries from working list
            $this->menu->getFile(),
            $this->menu->getName(),
            $this->menu->getTitle(),
            $this->menu->getDisplayCount()
        );
        foreach ($this->workList as &$item) {
            $this->menu->addItem($item);
        }
        $this->metaSource->save($this->menu);
    }

    protected function sortItems(): void
    {
        uasort($this->workList, [$this, 'sortWorkList']);
    }

    public function sortWorkList(Menu\Item $a, Menu\Item $b)
    {
        return $a->getPosition() <=> $b->getPosition();
    }
}
