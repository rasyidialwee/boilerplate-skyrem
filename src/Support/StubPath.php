<?php

namespace Skyrem\Boilerplate\Support;

final class StubPath
{
    public static function root(): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'stubs';
    }

    public static function shared(): string
    {
        return self::root().DIRECTORY_SEPARATOR.'shared';
    }

    public static function stack(string $frontend): string
    {
        return self::root().DIRECTORY_SEPARATOR.'stacks'.DIRECTORY_SEPARATOR.$frontend;
    }

    public static function fragment(string $name): string
    {
        return self::root().DIRECTORY_SEPARATOR.'fragments'.DIRECTORY_SEPARATOR.$name;
    }
}
