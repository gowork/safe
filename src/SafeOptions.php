<?php declare(strict_types=1);

namespace GW\Safe;

use Symfony\Component\Console\Input\InputInterface;

final class SafeOptions implements SafeAccessor
{
    use SafeAccessorTrait;

    private InputInterface $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function value(string $key, $default)
    {
        return $this->input->getOption($key) ?? $default;
    }
}
