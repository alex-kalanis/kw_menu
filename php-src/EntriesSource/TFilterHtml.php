<?php

namespace kalanis\kw_menu\DataSource;


use SplFileInfo;


/**
 * Trait TFilterHtml
 * @package kalanis\kw_menu\DataSource
 */
trait TFilterHtml
{
    protected static $allowedExtensions = ['htm', 'html', 'xhtm', 'xhtml'];

    public function filterExt(string $ext): bool
    {
        return in_array($ext, static::$allowedExtensions);
    }
}
