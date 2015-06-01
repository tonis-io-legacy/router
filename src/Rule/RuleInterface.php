<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\RequestInterface;
use Tonis\Router\Match;

interface RuleInterface
{
    /**
     * @param RequestInterface $request
     * @param Match $match
     * @return bool
     */
    public function __invoke(RequestInterface $request, Match $match);
}
