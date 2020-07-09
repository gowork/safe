<?php declare(strict_types=1);

namespace GW\Safe;

use InvalidArgumentException;
use function array_filter;
use function array_map;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_scalar;
use function method_exists;

trait SafeAccessorTrait
{
    /**
     * @param mixed $default
     * @return mixed
     */
    abstract public function value(string $key, $default);

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->value($key, $default);

        if (is_scalar($value)) {
            return (bool)$value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be bool");
    }

    public function string(string $key, string $default = ''): string
    {
        $value = $this->stringNullable($key, $default);

        if ($value !== null) {
            return $value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be string");
    }

    public function stringNullable(string $key, ?string $default = null): ?string
    {
        $value = $this->value($key, $default);

        if ($value === null) {
            return null;
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string)$value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be string");
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->intNullable($key, $default);

        if ($value !== null) {
            return $value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be int");
    }

    public function intNullable(string $key, ?int $default = null): ?int
    {
        $value = $this->value($key, $default);

        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value) || is_bool($value)) {
            return (int)$value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be int");
    }

    public function float(string $key, float $default = 0.0): float
    {
        $value = $this->floatNullable($key, $default);

        if ($value !== null) {
            return $value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be int");
    }

    public function floatNullable(string $key, ?float $default = null): ?float
    {
        $value = $this->value($key, $default);

        if ($value === null) {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be float");
    }

    /**
     * @return string[]
     */
    public function strings(string $key): array
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_map('strval', $value);
    }

    /**
     * @return int[]
     */
    public function ints(string $key): array
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_map('intval', $value);
    }

    /**
     * @return float[]
     */
    public function floats(string $key): array
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_map('floatval', array_filter($value, 'is_numeric'));
    }

    public function array(string $key): SafeAssocArray
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            throw new InvalidArgumentException("Value of {$key} cannot be array");
        }

        return SafeAssocArray::from($value);
    }

	public function list(string $key): SafeAssocList
	{
		$value = $this->value($key, null) ?? [];

		if (!is_array($value)) {
			throw new InvalidArgumentException("Value of {$key} cannot be array");
		}

		return SafeAssocList::fromArray($value);
	}
}
