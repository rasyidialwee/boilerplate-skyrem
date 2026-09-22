<?php

namespace Skyrem\Boilerplate\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Skyrem\Boilerplate\SkyremBoilerplateServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SkyremBoilerplateServiceProvider::class,
        ];
    }
}
