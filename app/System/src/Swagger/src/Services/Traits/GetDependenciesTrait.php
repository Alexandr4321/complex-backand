<?php

namespace App\System\Swagger\Services\Traits;

use ReflectionMethod;
use ReflectionFunctionAbstract;
use ReflectionParameter;

trait GetDependenciesTrait
{
    /**
     * @param  array  $parameters
     * @param $instance
     * @param $method
     * @return array
     * @throws \ReflectionException
     */
    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (! method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->getDependencies(
            new ReflectionMethod($instance, $method)
        );
    }
    
    /**
     * @param  ReflectionFunctionAbstract  $reflector
     * @return array
     */
    public function getDependencies(ReflectionFunctionAbstract $reflector)
    {
        return array_map(function ($parameter) {
            return $this->transformDependency($parameter);
        }, $reflector->getParameters());
    }
    
    /**
     * @param  ReflectionParameter  $parameter
     * @return null
     */
    protected function transformDependency(ReflectionParameter $parameter)
    {
        $class = $parameter->getClass();

        if (empty($class)) {
            return null;
        }

        return $class->name;
    }
}
