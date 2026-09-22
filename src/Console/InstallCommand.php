<?php

namespace Skyrem\Boilerplate\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Installers\ConfigureEnvironment;
use Skyrem\Boilerplate\Installers\CopyStubs;
use Skyrem\Boilerplate\Installers\InstallDependencies;
use Skyrem\Boilerplate\Installers\MergeComposerJson;
use Skyrem\Boilerplate\Installers\MergePackageJson;
use Skyrem\Boilerplate\Installers\RunArtisanTasks;
use Skyrem\Boilerplate\Support\InstallChoices;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    protected $signature = 'skyrem:install
        {--skip-packages : Skip Composer and npm dependency installation}
        {--skip-npm : Skip npm install only}
        {--skip-migrate : Skip migrate --seed}
        {--force : Skip the overwrite confirmation}';

    protected $description = 'Install the Skyrem boilerplate stack into this Laravel application';

    public function handle(Filesystem $files): int
    {
        $projectPath = base_path();

        if (! $this->guardLaravelVersion()) {
            return self::FAILURE;
        }

        if (! $this->option('force') && $this->input->isInteractive()) {
            $confirmed = confirm(
                label: 'Skyrem will overwrite application files with stubs. Continue?',
                default: false
            );

            if (! $confirmed) {
                $this->components->warn('Installation cancelled.');

                return self::SUCCESS;
            }
        }

        $choices = $this->resolveChoices();

        $this->components->info('Installing Skyrem boilerplate...');
        $this->line('  Frontend: '.$choices->frontend);
        $this->line('  Database: '.$choices->database);
        $this->line('  Dev tools: '.$choices->devtools);
        $this->newLine();

        try {
            (new CopyStubs($files))->handle($projectPath, $choices);
            $this->components->task('Copied application stubs');

            (new MergeComposerJson($files))->handle($projectPath, $choices);
            $this->components->task('Merged composer.json');

            (new MergePackageJson($files))->handle($projectPath, $choices);
            $this->components->task('Merged package.json');

            (new ConfigureEnvironment($files))->handle($projectPath, $choices);
            $this->components->task('Configured environment & Sail compose');

            $skipPackages = (bool) $this->option('skip-packages');
            $skipNpm = $skipPackages || (bool) $this->option('skip-npm');

            if (! $skipPackages || ! $skipNpm) {
                (new InstallDependencies)->handle(
                    $projectPath,
                    $skipPackages,
                    $skipNpm,
                    fn (string $line) => $this->line('  '.$line)
                );
                $this->components->task('Installed dependencies');
            } else {
                $this->components->twoColumnDetail('Dependencies', 'skipped');
            }

            if (! $skipPackages) {
                (new RunArtisanTasks)->handle(
                    $projectPath,
                    (bool) $this->option('skip-migrate'),
                    fn (string $line) => $this->line('  '.$line)
                );
                $this->components->task('Ran Artisan setup tasks');
            }
        } catch (\Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Skyrem boilerplate installed successfully.');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  1. Review .env (copy from .env.example / .env.dev.example if needed)');
        $this->line('  2. Start Sail: ./vendor/bin/sail up -d');
        $this->line('  3. Remove the installer package:');
        $this->line('     composer remove skyrem/boilerplate --dev');
        $this->newLine();

        return self::SUCCESS;
    }

    private function guardLaravelVersion(): bool
    {
        $version = app()->version();

        if (! str_starts_with($version, '13.')) {
            $this->components->error("Skyrem boilerplate requires Laravel 13. Detected: {$version}");

            return false;
        }

        return true;
    }

    private function resolveChoices(): InstallChoices
    {
        if (! $this->input->isInteractive()) {
            return InstallChoices::defaults();
        }

        $frontend = select(
            label: 'Frontend stack',
            options: InstallChoices::frontendOptions(),
            default: InstallChoices::FRONTEND_REACT_INERTIA
        );

        $database = select(
            label: 'Database & local services',
            options: InstallChoices::databaseOptions(),
            default: InstallChoices::DATABASE_SAIL_MYSQL_REDIS
        );

        $devtools = select(
            label: 'Dev tools preset',
            options: InstallChoices::devtoolsOptions(),
            default: InstallChoices::DEVTOOLS_SKYREM_FULL
        );

        return new InstallChoices(
            frontend: $frontend,
            database: $database,
            devtools: $devtools,
        );
    }
}
