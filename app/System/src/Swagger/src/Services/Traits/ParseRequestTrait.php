<?php

namespace App\System\Swagger\Services\Traits;

use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Minime\Annotations\Reader as AnnotationReader;

trait ParseRequestTrait
{
    
    /**
     *
     */
    protected function parseRequest()
    {
        $this->saveSecurity();
        
        $controller = $this->request->route()->getActionName();
        
        if ($controller === 'Closure') {
            $this->item['description'] = '';
            return;
        }
        
        $explodedController = explode('@', $controller);
        
        $controller = $explodedController[0];
        $action = $explodedController[1];
        
        $this->saveDescription($controller, $action);
        $this->saveParameters($this->getRequestClass($controller, $action));
    }
    
    /**
     *
     */
    protected function saveSecurity()
    {
        if ($this->security === 'jwt') {
            $header = $this->request->header('authorization');
        } elseif ($this->security === 'laravel') {
            $header = $this->request->cookie('__ym_uid');
        }
        
        if (!empty($header)) {
            $security = &$this->data['paths'][$this->uri][$this->method]['security'];
            if (empty($security)) {
                $security[] = [
                    "{$this->security}" => []
                ];
            }
        }
    }
    
    /**
     * @param $controller
     * @param $action
     * @return mixed
     */
    protected function getRequestClass($controller, $action)
    {
        $instance = app($controller);
        $route = $this->request->route();
        
        $parameters = $this->resolveClassMethodDependencies($route->parametersWithoutNulls(), $instance, $action);
        
        return Arr::first($parameters, function ($key, $parameter) {
            return preg_match('/Request/', $key);
        });
    }
    
    /**
     * @param $controller
     * @param $action
     */
    protected function saveDescription($controller, $action)
    {
        $actionInfo = $this->annotationReader->getMethodAnnotations($controller, $action);
        $controllerInfo = $this->annotationReader->getClassAnnotations($controller);
        
        $summary = $actionInfo->get('summary', '');
        $description = $actionInfo->get('description', '');
        $group = $actionInfo->get('group', false) ?: $controllerInfo->get('group', 'other');
        
        $summary = gettype($summary) === 'boolean' ? '' : $summary;
        $description = gettype($description) === 'boolean' ? '' : $description;
        
        $this->item['summary'] = $summary;
        $this->item['description'] = $description;
        $this->item['tags'] = [$group,];
    }
    
    /**
     * @param $requestClass
     */
    protected function saveParameters($requestClass)
    {
        if (!in_array($this->method, ['get', 'delete'])) {
            $this->savePostRequestParameters($requestClass);
        } else if ($this->method === 'get') {
            $this->saveGetRequestParameters($requestClass);
        }
    }
    
    /**
     * @param $requestClass
     */
    protected function saveGetRequestParameters($requestClass)
    {
        if ($requestClass) {
            $parameters = $this->annotationReader->getMethodAnnotations($requestClass, 'rules');
            foreach ($parameters as $name => $desc) {
                $this->item['parameters'][] = [
                    'in' => 'query',
                    'name' => $name,
                    'description' => $desc,
                    'schema' => [
                        'type' => 'string',
                    ],
                ];
            }
        }
    }
    
    /**
     * @param $requestClass
     */
    protected function savePostRequestParameters($requestClass)
    {
        $parameters = $this->data['paths'][$this->uri][$this->method]['parameters'];
        
        $bodyParamExisted = Arr::where($parameters, function ($value, $key) {
            return $value['name'] == 'body';
        });
        
        if (empty($bodyParamExisted)) {
//            if (empty($requestClass)) {
            $actionName = Str::camel(preg_replace('[\/]', '', $this->uri)).$this->method.'Request';
//                $actionDesc = '';
//            } else {
//                $info = $this->annotationReader->getClassAnnotations($requestClass);
//                $actionName = $info->get('name');
//                $actionDesc = $info->get('description');
//            }
            
            $this->item['parameters'][] = [
                'in' => 'query',
                'name' => 'body',
//                'description' => $actionDesc,
                'required' => true,
                'schema' => [
                    "\$ref" => "#/definitions/{$actionName}"
                ]
            ];
            
            $this->saveDefinitions($actionName, $requestClass);
        }
    }
    
    /**
     * @param $objectName
     * @param $requestClass
     */
    protected function saveDefinitions($objectName, $requestClass)
    {
        $data = [
            'type' => 'object',
            'properties' => []
        ];
        if ($requestClass) {
            $rules = (new $requestClass)->rules();
            $rulesInfo = $this->annotationReader->getMethodAnnotations($requestClass, 'rules');
            
            foreach ($rules as $parameter => $r) {
                $rulesArray = is_string($r) ? explode('|', $r) : $r;
                
                $data['properties'][$parameter] = [
                    'type' => $this->getParameterRulesDescription($rulesArray),
                    'description' => $rulesInfo->get($parameter, ''),
                ];
                
                if (in_array('required', $rulesArray)) {
                    $data['required'][] = $parameter;
                }
            }
        }
        
        $data['example'] = json_encode($this->generateExample($data['properties']));
        $this->data['definitions'][$objectName] = $data;
    }
    
    /**
     * @param $rules
     * @return string
     */
    protected function getParameterRulesDescription($rules)
    {
        $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
        
        $newRules = [];
        
        foreach ($rulesArray as $rule) {
            if (explode(':', $rule)[0] == 'unique') {
                $rule = 'unique';
            }
            $newRules[] = $rule;
        }
        
        return implode(', ', $newRules);
    }
    
    /**
     * @param $properties
     * @return array
     */
    protected function generateExample($properties)
    {
        $parameters = $this->replaceObjectValues($this->request->all());
        $example = [];
        
        $this->replaceNullValues($parameters, $properties, $example);
        return $example;
    }
    
    /**
     * @param $parameters
     * @return array
     */
    protected function replaceObjectValues($parameters)
    {
        $classNamesValues = [
            File::class => '[uploaded_file]',
        ];
        
        $parameters = Arr::dot($parameters);
        $returnParameters = [];
        
        foreach ($parameters as $parameter => $value) {
            if (is_object($value)) {
                $class = get_class($value);
                
                $value = Arr::get($classNamesValues, $class, $class);
            }
            
            Arr::set($returnParameters, $parameter, $value);
        }
        
        return $returnParameters;
    }
}
