<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\ServerRequestInterface;
use Tonis\Router\Match;

class Method implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ServerRequestInterface $request, Match $match)
    {
        $route = $match->getRoute();
        $methods = $route->getMethods();

        if (empty($methods)) {
            return true;
        }

        $method = $request->getMethod();
        foreach ($methods as $allowed) {
            if (0 === strcasecmp($method, $allowed)) {
                return true;
            }
        }

        return false;
    }
}
