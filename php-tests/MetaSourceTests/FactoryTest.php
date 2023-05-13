<?php

namespace MetaSourceTests;


use kalanis\kw_files\Access\Factory as composite_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_menu\MetaSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Storage\Key;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\Storage\Target;


class FactoryTest extends \CommonTestClass
{
    /**
     * @param mixed $input
     * @param string $expectedClass
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     * @dataProvider paramsProvider
     */
    public function testLibs($input, string $expectedClass): void
    {
        $lib = new MetaSource\Factory();
        $this->assertInstanceOf($expectedClass, $lib->getSource($input));
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return array<array<string|object|array<string, string|object>>>
     */
    public function paramsProvider(): array
    {
        return [
            ['root-path-somewhere', MetaSource\Volume::class],
            [(new composite_factory())->getClass('root-path-somewhere'), MetaSource\Files::class],
            [new Storage(new Key\DefaultKey(), new Target\Memory()), MetaSource\Storage::class],
            [['source' => 'root-path-somewhere'], MetaSource\Volume::class],
            [['source' => new Storage(new Key\DefaultKey(), new Target\Memory())], MetaSource\Storage::class],
            [['source' => new MetaSource\Volume('root-path-somewhere', new MetaSource\FileParser())], MetaSource\Volume::class],
        ];
    }

    /**
     * @throws FilesException
     * @throws MenuException
     * @throws PathsException
     */
    public function testFailed(): void
    {
        $lib = new MetaSource\Factory();
        $this->expectException(MenuException::class);
        $lib->getSource(new \stdClass());
    }
}
