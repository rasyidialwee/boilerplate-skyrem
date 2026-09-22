<?php

namespace Skyrem\Boilerplate;

use Illuminate\Support\ServiceProvider;
use Skyrem\Boilerplate\Console\InstallCommand;

class SkyremBoilerplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
