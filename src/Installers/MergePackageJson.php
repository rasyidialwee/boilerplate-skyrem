<?php

namespace Skyrem\Boilerplate\Installers;

use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Support\InstallChoices;
use Skyrem\Boilerplate\Support\StubPath;

final class MergePackageJson
{
    public function __construct(private Filesystem $files) {}

    public function handle(string $projectPath, InstallChoices $choices): void
    {
        $packagePath = $projectPath.DIRECTORY_SEPARATOR.'package.json';
        $fragmentPath = StubPath::fragment('package.'.$choices->frontend.'.json');

        if (! $this->files->exists($fragmentPath)) {
            throw new \RuntimeException("package.json fragment missing: {$fragmentPath}");
        }

        /** @var array<string, mixed> $fragment */
        $fragment = json_decode($this->files->get($fragmentPath), true, 512, JSON_THROW_ON_ERROR);

        if ($this->files->exists($packagePath)) {
            /** @var array<string, mixed> $package */
            $package = json_decode($this->files->get($packagePath), true, 512, JSON_THROW_ON_ERROR);
        } else {
            $package = [
                'private' => true,
                'type' => 'module',
            ];
        }

        foreach (['dependencies', 'devDependencies', 'optionalDependencies', 'scripts'] as $section) {
            if (! isset($fragment[$section]) || ! is_array($fragment[$section])) {
                continue;
            }

            $package[$section] = array_merge(
                is_array($package[$section] ?? null) ? $package[$section] : [],
                $fragment[$section]
            );

            if ($section !== 'scripts') {
                ksort($package[$section]);
            }
        }

        if (isset($fragment['type'])) {
            $package['type'] = $fragment['type'];
        }

        $this->files->put(
            $packagePath,
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );
    }
}
