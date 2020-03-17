<?php declare(strict_types=1);

namespace GW\Safe;

use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SafeRequest
{
    use SafeAccessorTrait;

    private Request $request;

    private function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function from(Request $request): self
    {
        return new self($request);
    }

    public static function mustBeFrom(?Request $request): self
    {
        if ($request === null) {
            throw new LogicException('Request is required');
        }

        return new self($request);
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function query(): SafeParameterBag
    {
        return SafeParameterBag::from($this->request->query);
    }

    public function post(): SafeParameterBag
    {
        return SafeParameterBag::from($this->request->request);
    }

    public function attributes(): SafeParameterBag
    {
        return SafeParameterBag::from($this->request->attributes);
    }

    public function ip(): string
    {
        $ip = $this->request->getClientIp();

        if ($ip === null) {
            throw new LogicException('Ip not set in request');
        }

        return $ip;
    }

    public function ipElse(string $default = 'unknown'): string
    {
        return $this->request->getClientIp() ?? $default;
    }

    public function session(): SessionInterface
    {
        return $this->request->getSession();
    }

    /**
     * @return mixed
     */
    public function value(string $key, $default)
    {
        return $this->request->get($key, $default);
    }
}
