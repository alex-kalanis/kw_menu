<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;


class VolumeTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     * @throws PathsException
     */
    public function testGetFiles(): void
    {
        $lib = new EntriesSource\Volume($this->getTargetPath());
        $iter = $lib->getFiles([]);
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));

        $iter2 = $lib->getFiles(['dummy3']);
        $files2 = iterator_to_array($iter2);
        $this->assertFalse(in_array('dummy4.htm', $files2));
        $this->assertTrue(in_array('dummy5.htm', $files2));
    }
}
