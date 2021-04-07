<?php declare(strict_types=1);

namespace GW\Safe;

use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SafeParameterBag
{
    use SafeAccessorTrait;

    private ParameterBag $params;

    private function __construct(ParameterBag $params)
    {
        $this->params = $params;
    }

    public static function from(ParameterBag $params): self
    {
        return new self($params);
    }

    /**
     * @return mixed
     */
    public function value(string $key, $default)
    {
        if ($this->params instanceof InputBag) {
            return $this->params->all()[$key] ?? $default;
        }

        return $this->params->get($key, $default);
    }
}
