<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\RouteMatch;

class Path implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(RequestInterface $request, RouteMatch $match)
    {
        $route = $match->getRoute();
        if (preg_match('@^' . $route->getRegex() . '$@', $request->getUri()->getPath(), $params)) {
            foreach ($params as $index => $param) {
                if (is_numeric($index)) {
                    unset($params[$index]);
                }
            }
            $match->setParams(array_merge($route->getDefaults(), $params));
            return true;
        }
        return false;
    }
}
