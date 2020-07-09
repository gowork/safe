<?php declare(strict_types=1);

namespace GW\Safe;

use Countable;
use function array_filter;
use function array_map;
use function array_values;
use function count;

final class SafeAssocList implements Countable
{
	/** @var SafeAssocArray[] */
	private array $items;

	private function __construct(SafeAssocArray ...$items)
	{
		$this->items = $items;
	}

	public static function from(SafeAssocArray ...$items): self
	{
		return new self(...$items);
	}

	/** @param array<int, array<string|int, mixed>> $items */
	public static function fromArray(array $items): self
	{
		return self::from(
			...array_map([SafeAssocArray::class, 'from'], array_values(array_filter($items, 'is_array')))
		);
	}

	/** @return SafeAssocArray[] */
	public function toArray(): array
	{
		return $this->items;
	}

	public function count(): int
	{
		return count($this->items);
	}

	/**
	 * @template T
	 * @phpstan-param callable(SafeAssocArray):T $map
	 * @phpstan-return T[]
	 */
	public function map(callable $map): array
	{
		return array_map($map, $this->items);
	}

	/**
	 * @phpstan-param callable(SafeAssocArray):bool $filter
	 * @phpstan-return T[]
	 */
	public function filter(callable $filter): self
	{
		return new self(...array_values(array_filter($this->items, $filter)));
	}
}
