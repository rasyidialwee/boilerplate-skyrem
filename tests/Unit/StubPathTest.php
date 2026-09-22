<?php

use Skyrem\Boilerplate\Support\StubPath;

it('points stub root at the package stubs directory', function () {
    expect(StubPath::root())->toEndWith(DIRECTORY_SEPARATOR.'stubs')
        ->and(is_dir(StubPath::root()))->toBeTrue();
});
