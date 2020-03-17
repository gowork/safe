<?php declare(strict_types=1);

namespace GW\Safe;

interface SafeAccessor
{
    public function bool(string $key, bool $default = false): bool;

    public function string(string $key, string $default = ''): string;

    public function stringNullable(string $key, ?string $default = null): ?string;

    public function int(string $key, int $default = 0): int;

    public function intNullable(string $key, ?int $default = null): ?int;

    public function float(string $key, float $default = 0.0): float;

    public function floatNullable(string $key, ?float $default = null): ?float;

    /**
     * @return string[]
     */
    public function strings(string $key): array;

    /**
     * @return int[]
     */
    public function ints(string $key): array;
}
