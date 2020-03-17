<?php declare(strict_types=1);

namespace GW\Safe;

use Symfony\Component\Console\Input\InputInterface;

final class SafeConsoleInput
{
    public static function options(InputInterface $input): SafeAccessor
    {
        return new SafeOptions($input);
    }

    public static function arguments(InputInterface $input): SafeAccessor
    {
        return new SafeArguments($input);
    }
}
