<?php

namespace Skyrem\Boilerplate\Installers;

use Symfony\Component\Process\Process;

final class InstallDependencies
{
    /**
     * @param  callable(string): void  $line
     */
    public function handle(string $projectPath, bool $skipComposer, bool $skipNpm, callable $line): void
    {
        if (! $skipComposer) {
            $line('Running composer update...');
            $this->run(['composer', 'update', '--no-interaction'], $projectPath, $line);
        }

        if (! $skipNpm) {
            $line('Running npm install...');
            $this->run(['npm', 'install'], $projectPath, $line);
        }
    }

    /**
     * @param  list<string>  $command
     * @param  callable(string): void  $line
     */
    private function run(array $command, string $cwd, callable $line): void
    {
        $process = new Process($command, $cwd, timeout: 600);
        $process->run(function (string $type, string $buffer) use ($line): void {
            foreach (explode(PHP_EOL, rtrim($buffer, PHP_EOL)) as $outputLine) {
                if ($outputLine !== '') {
                    $line($outputLine);
                }
            }
        });

        if (! $process->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                'Command failed (%s): %s',
                implode(' ', $command),
                $process->getErrorOutput() ?: $process->getOutput()
            ));
        }
    }
}
