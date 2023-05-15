<?php

namespace TraitsTests;


use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_menu\Translations;


class LangTest extends \CommonTestClass
{
    public function testSimple(): void
    {
        $lib = new XLang();
        $this->assertNotEmpty($lib->getMnLang());
        $this->assertInstanceOf(Translations::class, $lib->getMnLang());
        $lib->setMnLang(new XTrans());
        $this->assertInstanceOf(XTrans::class, $lib->getMnLang());
        $lib->setMnLang(null);
        $this->assertInstanceOf(Translations::class, $lib->getMnLang());
    }
}


class XLang
{
    use TLang;
}


class XTrans implements IMNTranslations
{
    public function mnCannotOpen(): string
    {
        return 'mock';
    }

    public function mnCannotSave(): string
    {
        return 'mock';
    }

    public function mnItemNotFound(string $item): string
    {
        return 'mock';
    }

    public function mnProblematicData(): string
    {
        return 'mock';
    }

    public function mnNoAvailableEntrySource(): string
    {
        return 'mock';
    }

    public function mnNoAvailableMetaSource(): string
    {
        return 'mock';
    }

}
