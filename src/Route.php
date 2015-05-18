<?php

namespace Tonis\Router;

use Psr\Http\Message\RequestInterface;

final class Route
{
    /** @var string */
    private $name;
    /** @var string */
    private $path;
    /** @var mixed */
    private $handler;
    /** @var array */
    private $defaults = [];
    /** @var array */
    private $accept = [];
    /** @var array */
    private $methods = ['GET'];
    /** @var string */
    private $regex;
    /** @var null|\SplFixedArray */
    protected $tokens;

    /**
     * @param string $name
     * @param string $path
     * @param string $handler
     */
    public function __construct($name, $path, $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception\MissingParameterException
     */
    public function assemble(array $params = [])
    {
        $this->init();
        if ($this->tokens) {
            foreach ($this->tokens as $token) {
                list($name, $optional) = $token;
                if ($optional || isset($params[$name])) {
                    continue;
                }
                throw new Exception\MissingParameterException($this->getName(), $name);
            }
        }
        $replace = function ($matches) use ($params) {
            if (isset($params[$matches[2]])) {
                return $matches[1] . $params[$matches[2]];
            }
            return '';
        };
        return preg_replace_callback('@{([^A-Za-z]*)([A-Za-z]+)[?]?(?::[^}]+)?}@', $replace, $this->path);
    }

    public function accept(array $accept)
    {
        $this->accept = $accept;
        return $this;
    }

    public function defaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function methods(array $methods)
    {
        foreach ($methods as &$method) {
            $method = strtoupper($method);
        }
        $this->methods = $methods;
        return $this;
    }

    public function match(RequestInterface $request)
    {
        $this->init();
        if (!empty($this->methods)) {
            $method = $request->getMethod();
            if (!in_array($method, $this->methods)) {
                return null;
            }
        }
        if (preg_match('@^' . $this->regex . '$@', $request->getUri()->getPath(), $matches)) {
            foreach ($matches as $index => $match) {
                if (is_numeric($index)) {
                    unset($matches[$index]);
                }
            }
            return new RouteMatch($this, array_merge($this->defaults, $matches));
        }
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    private function init()
    {
        if ($this->regex) {
            return;
        }
        $this->regex = $this->path;
        $matches = [];
        if ($count = preg_match_all('@{([^A-Za-z]*([A-Za-z]+))([?]?)(?::([^}]+))?}@', $this->regex, $matches)) {
            $this->tokens = new \SplFixedArray($count);
            foreach ($matches[1] as $index => $token) {
                $fullString = $matches[1][$index];
                $name = $matches[2][$index];
                $optional = !empty($matches[3][$index]);
                $constraint = empty($matches[4][$index]) ? '.*' : $matches[4][$index];
                if ($optional) {
                    $replace = sprintf('(?:%s(?<%s>%s))?', str_replace($name, '', $fullString), $name, $constraint);
                } else {
                    $replace = sprintf('(?<%s>%s)', $name, $constraint);
                }
                $this->regex = str_replace($matches[0][$index], $replace, $this->regex);
                $this->tokens[$index] = [$name, $optional];
            }
        }
    }
}
