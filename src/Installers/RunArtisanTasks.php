<?php

namespace Skyrem\Boilerplate\Installers;

use Symfony\Component\Process\Process;

final class RunArtisanTasks
{
    /**
     * @param  callable(string): void  $line
     */
    public function handle(string $projectPath, bool $skipMigrate, callable $line): void
    {
        $tasks = [
            ['php', 'artisan', 'key:generate', '--force'],
            ['php', 'artisan', 'storage:link', '--force'],
            ['php', 'artisan', 'package:discover', '--ansi'],
        ];

        if (! $skipMigrate) {
            $tasks[] = ['php', 'artisan', 'migrate', '--force', '--seed'];
        }

        foreach ($tasks as $command) {
            $line('Running '.implode(' ', array_slice($command, 2)).'...');
            $this->run($command, $projectPath, $line);
        }
    }

    /**
     * @param  list<string>  $command
     * @param  callable(string): void  $line
     */
    private function run(array $command, string $cwd, callable $line): void
    {
        $process = new Process($command, $cwd, timeout: 300);
        $process->run(function (string $type, string $buffer) use ($line): void {
            foreach (explode(PHP_EOL, rtrim($buffer, PHP_EOL)) as $outputLine) {
                if ($outputLine !== '') {
                    $line($outputLine);
                }
            }
        });

        if (! $process->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                'Artisan command failed (%s): %s',
                implode(' ', $command),
                $process->getErrorOutput() ?: $process->getOutput()
            ));
        }
    }
}
