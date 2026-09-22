<?php

namespace Skyrem\Boilerplate\Installers;

use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Support\InstallChoices;

final class ConfigureEnvironment
{
    public function __construct(private Filesystem $files) {}

    public function handle(string $projectPath, InstallChoices $choices): void
    {
        $envExample = $projectPath.DIRECTORY_SEPARATOR.'.env.example';
        $envDevExample = $projectPath.DIRECTORY_SEPARATOR.'.env.dev.example';
        $envPath = $projectPath.DIRECTORY_SEPARATOR.'.env';

        if ($this->files->exists($envDevExample) && ! $this->files->exists($envExample)) {
            $this->files->copy($envDevExample, $envExample);
        }

        if ($this->files->exists($envExample) && ! $this->files->exists($envPath)) {
            $this->files->copy($envExample, $envPath);
        }

        if ($choices->database === InstallChoices::DATABASE_SAIL_MYSQL_REDIS) {
            $composeDev = $projectPath.DIRECTORY_SEPARATOR.'compose-dev.yaml';
            $compose = $projectPath.DIRECTORY_SEPARATOR.'compose.yaml';

            if ($this->files->exists($composeDev) && ! $this->files->exists($compose)) {
                $this->files->copy($composeDev, $compose);
            }
        }
    }
}
