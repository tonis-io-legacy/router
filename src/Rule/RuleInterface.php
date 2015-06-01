<?php
namespace Tonis\Router\Rule;

use Psr\Http\Message\ServerRequestInterface;
use Tonis\Router\Match;

interface RuleInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param Match $match
     * @return bool
     */
    public function __invoke(ServerRequestInterface $request, Match $match);
}
