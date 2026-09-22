<?php

use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Installers\CopyStubs;
use Skyrem\Boilerplate\Installers\MergeComposerJson;
use Skyrem\Boilerplate\Installers\MergePackageJson;
use Skyrem\Boilerplate\Support\InstallChoices;
use Skyrem\Boilerplate\Support\StubPath;

it('registers the skyrem:install command', function () {
    $this->artisan('list')
        ->expectsOutputToContain('skyrem:install')
        ->assertSuccessful();
});

it('exposes default install choices', function () {
    $choices = InstallChoices::defaults();

    expect($choices->frontend)->toBe(InstallChoices::FRONTEND_REACT_INERTIA)
        ->and($choices->database)->toBe(InstallChoices::DATABASE_SAIL_MYSQL_REDIS)
        ->and($choices->devtools)->toBe(InstallChoices::DEVTOOLS_SKYREM_FULL);
});

it('defines a single option per install menu', function () {
    expect(InstallChoices::frontendOptions())->toHaveCount(1)
        ->and(InstallChoices::databaseOptions())->toHaveCount(1)
        ->and(InstallChoices::devtoolsOptions())->toHaveCount(1);
});

it('resolves stub directories for the react-inertia stack', function () {
    expect(StubPath::shared())->toBeDirectory()
        ->and(StubPath::stack(InstallChoices::FRONTEND_REACT_INERTIA))->toBeDirectory()
        ->and(StubPath::fragment('composer.react-inertia.json'))->toBeFile()
        ->and(StubPath::fragment('package.react-inertia.json'))->toBeFile();
});

it('copies stubs and merges composer and package fragments', function () {
    $files = new Filesystem;
    $temp = sys_get_temp_dir().'/skyrem-boilerplate-'.uniqid();
    $files->makeDirectory($temp);

    $files->put($temp.'/composer.json', json_encode([
        'name' => 'acme/app',
        'require' => [
            'php' => '^8.4',
            'laravel/framework' => '^13.0',
            'skyrem/boilerplate' => '*',
        ],
        'require-dev' => [],
        'scripts' => [],
    ], JSON_PRETTY_PRINT));

    $files->put($temp.'/package.json', json_encode([
        'private' => true,
        'type' => 'module',
        'dependencies' => [],
        'devDependencies' => [],
        'scripts' => [],
    ], JSON_PRETTY_PRINT));

    $choices = InstallChoices::defaults();

    (new CopyStubs($files))->handle($temp, $choices);
    (new MergeComposerJson($files))->handle($temp, $choices);
    (new MergePackageJson($files))->handle($temp, $choices);

    expect($temp.'/app')->toBeDirectory()
        ->and($temp.'/resources/js')->toBeDirectory()
        ->and($temp.'/bin/php')->toBeFile()
        ->and($temp.'/compose-dev.yaml')->toBeFile()
        ->and($temp.'/.github/workflows')->toBeDirectory()
        ->and($temp.'/pint.json')->toBeFile();

    $composer = json_decode($files->get($temp.'/composer.json'), true);
    expect($composer['require'])->toHaveKey('spatie/laravel-permission')
        ->and($composer['require'])->toHaveKey('laravel/horizon')
        ->and($composer['require'])->toHaveKey('skyrem/boilerplate')
        ->and($composer['require-dev'])->toHaveKey('larastan/larastan')
        ->and($composer['scripts'])->toHaveKey('phpstan');

    $package = json_decode($files->get($temp.'/package.json'), true);
    expect($package['dependencies'])->toHaveKey('@inertiajs/react')
        ->and($package['dependencies'])->toHaveKey('react');

    $files->deleteDirectory($temp);
});
