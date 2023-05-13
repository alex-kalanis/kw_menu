<?php

namespace MetaSourceTests;


use kalanis\kw_menu\Traits\TFilterHtml;


class FilterTest extends \CommonTestClass
{
    /**
     * @param string $ext
     * @param bool $result
     * @dataProvider filterDataProvider
     */
    public function testSimple(string $ext, bool $result): void
    {
        $lib = new XFilter();
        $this->assertEquals($result, $lib->filterExt($ext));
    }

    public function filterDataProvider(): array
    {
        return [
            ['', false],
            ['of', false],
            ['htaccess', false],
            ['.html', false],
            ['xml', false],
            ['html', true],
            ['xhtml', true],
        ];
    }
}


class XFilter
{
    use TFilterHtml;
}
