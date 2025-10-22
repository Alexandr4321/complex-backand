<?php

namespace App\System\Classes;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class AutoRoute
{
    private static $httpMethods = [ 'get', 'post', 'put', 'patch', 'delete', ];
    
    public static function controller($controllerClass)
    {
        $class = new ReflectionClass($controllerClass);
        $classDocs = $class->getDocComment();
        preg_match('/@alias (.+)/m', $classDocs, $matches);
        $basePath = trim(Arr::get($matches, 1, ''));
        
        $pieces = explode('\\', $controllerClass);
        $isWeb = $pieces[count($pieces) - 2] === 'Web';
        
        $publicMethods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        
        foreach ($publicMethods as $method) {
            $methodName = $method->name;
            foreach (self::$httpMethods as $httpMethod) {
                if ((substr($methodName, 0, strlen($httpMethod)) == $httpMethod)) {
                    $docs = $class->getMethod($methodName)->getDocComment();
                    preg_match('/@path (.+)/m', $docs, $matches);
                    $path = trim(Arr::get($matches, 1, ''));
                    preg_match('/@alias (.+)/m', $docs, $matches);
                    $name = trim(Arr::get($matches, 1, ''));
                    preg_match('/@version (.+)/m', $docs, $matches);
                    $version = trim(Arr::get($matches, 1, $isWeb ? '0' : '1'));
                    $version = in_array($version, [ 'false', '0', 'null' ]) ? '' : "v$version/";
                    if ($path) {
                        $route = Route::$httpMethod($version.$basePath.$path, $controllerClass.'@'.$methodName);
                        if ($name) {
                            $route->name($name);
                        }
                    }
                    break;
                }
            }
        }
    }
    
    public static function action()
    {
    
    }
}
