<?php declare(strict_types=1);

namespace GW\Safe;

use function property_exists;

final class SafeObject
{
    use SafeAccessorTrait;

    private object $object;

    private function __construct(object $object)
    {
        $this->object = $object;
    }

    public static function from(object $object): self
    {
        return new self($object);
    }

    /**
     * @return mixed
     */
    public function value(string $key, $default)
    {
        if (property_exists($this->object, $key)) {
            return $this->object->{$key};
        }

        return $default;
    }
}
