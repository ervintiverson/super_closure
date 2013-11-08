<?php

namespace Jeremeamia\SuperClosure;

class ClosureBinding
{
    /**
     * @var object The object that $this is bound to in the closure
     */
    protected $object;

    /**
     * @var string The scope class of the object that affects the visibility of the object's members to the closure
     */
    protected $scope;

    /**
     * @param \Closure $closure
     *
     * @return ClosureBinding
     */
    public static function fromClosure(\Closure $closure)
    {
        return self::fromReflection(new \ReflectionFunction($closure));
    }

    /**
     * @param \ReflectionFunction $reflection
     *
     * @return ClosureBinding
     * @throws \InvalidArgumentException
     */
    public static function fromReflection(\ReflectionFunction $reflection)
    {
        if (!$reflection->isClosure()) {
            throw new \InvalidArgumentException('Please provide the reflection of a closure.');
        }

        if (PHP_VERSION_ID < 50400) {
            // Only closures in PHP 5.4+ have bindings
            return new self(null, null);
        } else {
            /** @var \ReflectionFunction $scope */
            if ($scope = $reflection->getClosureScopeClass()) {
                $scope = $scope->getName();
            }

            return new self($reflection->getClosureThis(), $scope);
        }
    }

    /**
     * @param object $object
     * @param string $scope
     */
    public function __construct($object, $scope)
    {
        $this->object = $object;
        $this->scope = $scope;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
