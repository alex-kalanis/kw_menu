<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MenuException;


class VolumeTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testGetFiles(): void
    {
        $lib = new EntriesSource\Volume($this->getTargetPath());
        $iter = $lib->getFiles('');
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));
    }
}
