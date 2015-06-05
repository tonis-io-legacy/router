<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\RouteMatch;

interface RuleInterface
{
    /**
     * @param RequestInterface $request
     * @param RouteMatch $match
     * @return bool
     */
    public function __invoke(RequestInterface $request, RouteMatch $match);
}
