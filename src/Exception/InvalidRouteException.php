<?php
namespace Tonis\Router\Exception;

class InvalidRouteException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Route must be an instance of Tonis\Router\Route');
    }
}
