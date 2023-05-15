<?php

namespace TraitsTests;


use kalanis\kw_menu\Traits\TEscape;


class EscapeTest extends \CommonTestClass
{
    /**
     * @param string $ext
     * @dataProvider escapeProvider
     */
    public function testSimple(string $ext): void
    {
        $lib = new XEscape();
        $this->assertEquals($ext, $lib->restore($lib->escape($ext)));
    }

    public function escapeProvider(): array
    {
        return [
            ['sdf gf dg | gfdfghhb||gsdjhsd'],
            ["gfh \r gfsdgd\r\nncejka\tdgsd\n\tdfhf"],
        ];
    }
}


class XEscape
{
    use TEscape;

    public function escape(string $content): string
    {
        return $this->escapeNl($content);
    }

    public function restore(string $content): string
    {
        return $this->restoreNl($content);
    }
}
