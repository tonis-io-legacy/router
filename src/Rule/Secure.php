<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\Match;

class Secure implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(RequestInterface $request, Match $match)
    {
        $route = $match->getRoute();

        if (null === $route->getSecure()) {
            return true;
        }

        $uri = $request->getUri();
        return $route->getSecure() == ($uri->getScheme() === 'https' || $uri->getPort() == 443);
    }
}
