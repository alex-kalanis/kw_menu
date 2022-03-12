<?php

namespace EntriesSourceTests;


use kalanis\kw_menu\EntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Path;


class TreeTest extends \CommonTestClass
{
    /**
     * @throws MenuException
     */
    public function testGetFiles(): void
    {
        $path = new Path();
        $path->setDocumentRoot($this->getTargetPath());
        $lib = new EntriesSource\Tree($path);
        $iter = $lib->getFiles('');
        $files = iterator_to_array($iter);
        $this->assertNotEmpty($files);
        $this->assertTrue(in_array('dummy4.htm', $files));
        $this->assertFalse(in_array('dummy5.htm', $files));
    }
}
