<?php

use PHPUnit\Framework\TestCase;


/**
 * Class CommonTestClass
 * The structure for mocking and configuration seems so complicated, but it's necessary to let it be totally idiot-proof
 */
class CommonTestClass extends TestCase
{
    protected function tearDown(): void
    {
        $ptXM = $this->getTargetPath() . 'copy.meta';
        if (is_file($ptXM)) {
            @unlink($ptXM);
        }
    }

    protected function getTargetPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR;
    }
}
