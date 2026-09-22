<?php

namespace Skyrem\Boilerplate\Installers;

use Illuminate\Filesystem\Filesystem;
use Skyrem\Boilerplate\Support\InstallChoices;
use Skyrem\Boilerplate\Support\StubPath;
use Symfony\Component\Finder\Finder;

final class CopyStubs
{
    public function __construct(private Filesystem $files) {}

    public function handle(string $projectPath, InstallChoices $choices): void
    {
        $this->copyDirectory(StubPath::shared(), $projectPath);
        $this->copyDirectory(StubPath::stack($choices->frontend), $projectPath);
    }

    private function copyDirectory(string $from, string $to): void
    {
        if (! $this->files->isDirectory($from)) {
            throw new \RuntimeException("Stub directory missing: {$from}");
        }

        $this->files->ensureDirectoryExists($to);

        $finder = Finder::create()
            ->in($from)
            ->ignoreDotFiles(false)
            ->ignoreVCS(true);

        foreach ($finder->files() as $file) {
            $relative = $file->getRelativePathname();
            $destination = $to.DIRECTORY_SEPARATOR.$relative;
            $this->files->ensureDirectoryExists(dirname($destination));
            $this->files->copy($file->getPathname(), $destination);
        }
    }
}
