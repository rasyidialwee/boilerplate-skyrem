<?php

namespace Skyrem\Boilerplate\Support;

final class InstallChoices
{
    public const FRONTEND_REACT_INERTIA = 'react-inertia';

    public const DATABASE_SAIL_MYSQL_REDIS = 'sail-mysql-redis';

    public const DEVTOOLS_SKYREM_FULL = 'skyrem-full';

    public function __construct(
        public string $frontend = self::FRONTEND_REACT_INERTIA,
        public string $database = self::DATABASE_SAIL_MYSQL_REDIS,
        public string $devtools = self::DEVTOOLS_SKYREM_FULL,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function frontendOptions(): array
    {
        return [
            self::FRONTEND_REACT_INERTIA => 'React + Inertia + TypeScript + Tailwind 4 + Fortify + Wayfinder',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function databaseOptions(): array
    {
        return [
            self::DATABASE_SAIL_MYSQL_REDIS => 'MySQL 8 + Redis via Laravel Sail',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function devtoolsOptions(): array
    {
        return [
            self::DEVTOOLS_SKYREM_FULL => 'Skyrem full (Pint, Larastan, Rector, Pest Arch, Horizon, Telescope, Reverb, CI)',
        ];
    }

    public static function defaults(): self
    {
        return new self;
    }
}
