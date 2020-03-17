<?php declare(strict_types=1);

namespace GW\Safe;

use function array_key_exists;

final class SafeAssocArray
{
    use SafeAccessorTrait;

    /** @var array<string, mixed> */
    private array $array;

    private function __construct(array $array)
    {
        $this->array = $array;
    }

    public static function from(array $array): self
    {
        return new self($array);
    }

    /**
     * @return mixed
     */
    public function value(string $key, $default)
    {
        if (array_key_exists($key, $this->array)) {
            return $this->array[$key];
        }

        return $default;
    }
}
