<?php

namespace Skyrem\Boilerplate\Installers;

use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Support\InstallChoices;
use Skyrem\Boilerplate\Support\StubPath;

final class MergeComposerJson
{
    public function __construct(private Filesystem $files) {}

    public function handle(string $projectPath, InstallChoices $choices): void
    {
        $composerPath = $projectPath.DIRECTORY_SEPARATOR.'composer.json';
        $fragmentPath = StubPath::fragment('composer.'.$choices->frontend.'.json');

        if (! $this->files->exists($composerPath)) {
            throw new \RuntimeException('composer.json not found in project.');
        }

        if (! $this->files->exists($fragmentPath)) {
            throw new \RuntimeException("Composer fragment missing: {$fragmentPath}");
        }

        /** @var array<string, mixed> $composer */
        $composer = json_decode($this->files->get($composerPath), true, 512, JSON_THROW_ON_ERROR);

        /** @var array<string, mixed> $fragment */
        $fragment = json_decode($this->files->get($fragmentPath), true, 512, JSON_THROW_ON_ERROR);

        foreach (['require', 'require-dev'] as $section) {
            if (! isset($fragment[$section]) || ! is_array($fragment[$section])) {
                continue;
            }

            $composer[$section] = array_merge(
                is_array($composer[$section] ?? null) ? $composer[$section] : [],
                $fragment[$section]
            );

            ksort($composer[$section]);
        }

        if (isset($fragment['scripts']) && is_array($fragment['scripts'])) {
            $composer['scripts'] = array_merge(
                is_array($composer['scripts'] ?? null) ? $composer['scripts'] : [],
                $fragment['scripts']
            );
        }

        if (isset($fragment['autoload']['files']) && is_array($fragment['autoload']['files'])) {
            $composer['autoload'] = is_array($composer['autoload'] ?? null) ? $composer['autoload'] : [];
            $composer['autoload']['files'] = array_values(array_unique(array_merge(
                is_array($composer['autoload']['files'] ?? null) ? $composer['autoload']['files'] : [],
                $fragment['autoload']['files']
            )));
        }

        $this->files->put(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );
    }
}
