<?php declare(strict_types=1);

namespace GW\Safe;

use Dazet\TypeUtil\BooleanUtil;
use Dazet\TypeUtil\InvalidTypeException;
use Dazet\TypeUtil\NumberUtil;
use Dazet\TypeUtil\StringUtil;
use InvalidArgumentException;
use function array_filter;
use function array_map;
use function array_values;
use function is_array;

trait SafeAccessorTrait
{
    /**
     * @param mixed $default
     * @return mixed
     */
    abstract public function value(string $key, $default);

    public function bool(string $key, bool $default = false): bool
    {
        $value = BooleanUtil::toBoolOrNull($this->value($key, $default));

        if ($value === null) {
            throw new InvalidArgumentException("Value of {$key} cannot be bool");
        }

        return $value;
    }

    public function boolOrDefault(string $key, bool $default): bool
    {
        return BooleanUtil::toBoolOrNull($this->value($key, null)) ?? $default;
    }

    public function string(string $key, string $default = ''): string
    {
        $value = StringUtil::toStringOrNull($this->value($key, $default));

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

        if (StringUtil::canBeString($value)) {
            return StringUtil::toString($value);
        }

        throw new InvalidArgumentException("Value of {$key} cannot be string");
    }

    public function stringOrNull(string $key): ?string
    {
        return StringUtil::toStringOrNull($this->value($key, null));
    }

    public function stringOrDefault(string $key, string $default): string
    {
        return $this->stringOrNull($key) ?? $default;
    }

    public function int(string $key, int $default = 0): int
    {
        $value = NumberUtil::toIntOrNull($this->value($key, $default));

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

        if (NumberUtil::canBeNumber($value)) {
            return NumberUtil::toInt($value);
        }

        throw new InvalidArgumentException("Value of {$key} cannot be int");
    }

    public function intOrNull(string $key): ?int
    {
        return NumberUtil::toIntOrNull($this->value($key, null));
    }

    public function intOrDefault(string $key, int $default): int
    {
        return $this->intOrNull($key) ?? $default;
    }

    public function float(string $key, float $default = 0.0): float
    {
        $value = NumberUtil::toFloatOrNull($this->value($key, $default));

        if ($value !== null) {
            return $value;
        }

        throw new InvalidArgumentException("Value of {$key} cannot be float");
    }

    public function floatNullable(string $key, ?float $default = null): ?float
    {
        $value = $this->value($key, $default);

        if ($value === null) {
            return null;
        }

        if (NumberUtil::canBeNumber($value)) {
            return NumberUtil::toFloat($value);
        }

        throw new InvalidArgumentException("Value of {$key} cannot be float");
    }

    public function floatOrNull(string $key): ?float
    {
        return NumberUtil::toFloatOrNull($this->value($key, null));
    }

    public function floatOrDefault(string $key, float $default): float
    {
        return $this->floatOrNull($key) ?? $default;
    }

    /**
     * @return string[]
     */
    public function strings(string $key): array
    {
        return $this->arrayCast($key, 'string', StringUtil::toString);
    }

    /**
     * @return string[]
     */
    public function stringsFiltered(string $key): array
    {
        return $this->arrayCastFiltered($key, StringUtil::canBeString, StringUtil::toString);
    }

    /**
     * @return string[]
     */
    public function stringsForced(string $key, string $default = ''): array
    {
        return $this->arrayCast($key, 'string', fn($value): string => StringUtil::toStringOrNull($value) ?? $default);
    }

    /**
     * @return int[]
     */
    public function ints(string $key): array
    {
        return $this->arrayCast($key, 'int', NumberUtil::toInt);
    }

    /**
     * @return int[]
     */
    public function intsFiltered(string $key): array
    {
        return $this->arrayCastFiltered($key, NumberUtil::canBeNumber, NumberUtil::toInt);
    }

    /**
     * @return int[]
     */
    public function intsForced(string $key, int $default = 0): array
    {
        return $this->arrayCast($key, 'int', fn($value): int => NumberUtil::toIntOrNull($value) ?? $default);
    }

    /**
     * @return float[]
     */
    public function floats(string $key): array
    {
        return $this->arrayCast($key, 'float', NumberUtil::toFloat);
    }

    /**
     * @return float[]
     */
    public function floatsFiltered(string $key): array
    {
        return $this->arrayCastFiltered($key, NumberUtil::canBeNumber, NumberUtil::toFloat);
    }

    /**
     * @return int[]
     */
    public function floatsForced(string $key, float $default = 0): array
    {
        return $this->arrayCast($key, 'float', fn($value): float => NumberUtil::toFloatOrNull($value) ?? $default);
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

    /**
     * @template TOut
     * @phpstan-param string $key
     * @phpstan-param callable(mixed):TOut $utilCaster
     * @phpstan-return TOut[]
     */
	private function arrayCast(string $key, string $type, callable $utilCaster)
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            $value = [$value];
        }

        try {
            return array_map($utilCaster, $value);
        } catch (InvalidTypeException $utilError) {
            throw new InvalidArgumentException("Value of {$key} contains items that cannot be {$type}", 0, $utilError);
        }
    }

    /**
     * @template TOut
     * @phpstan-param string $key
     * @phpstan-param callable(mixed):bool $filter
     * @phpstan-param callable(mixed):TOut $caster
     * @phpstan-return TOut[]
     */
	private function arrayCastFiltered(string $key, callable $filter, callable $caster): array
    {
        $value = $this->value($key, null) ?? [];

        if (!is_array($value)) {
            $value = [$value];
        }

        $value = array_filter($value, fn($value) => $value !== null);
        $value = array_filter($value, $filter);
        $value = array_map($caster, $value);

        return array_values($value);
    }
}
