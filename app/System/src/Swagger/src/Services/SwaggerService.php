<?php

namespace App\System\Swagger\Services;

use App\System\Requests\Request;
use App\System\Resources\JsonResource;
use App\System\Swagger\OpenApi\OpenApi;
use App\System\Swagger\OpenApi\Path;
use App\System\Swagger\OpenApi\PathResponse;
use App\System\Swagger\OpenApi\RequestBody;
use App\System\Swagger\OpenApi\Schema;
use App\System\Swagger\OpenApi\Tag;
use App\System\Swagger\Services\Traits\GetDependenciesTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Unique;
use Minime\Annotations\Reader as AnnotationReader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\ArrayCache;

class SwaggerService
{
    use GetDependenciesTrait;
    
    /**
     * @var LocalDataCollector
     */
    protected $dataCollector;
    
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;
    
    /**
     * @var OpenApi
     */
    protected $openApi;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var Response
     */
    protected $response;
    
    protected $data;
    
    protected $security;
    
    
    protected $uri;
    
    protected $method;
    
    protected $item;
    
    /**
     * SwaggerService constructor.
     */
    public function __construct()
    {
        if (config('app.env') === 'testing' || true) {
            $this->dataCollector = app(LocalDataCollector::class);
            
            $this->openApi = $this->dataCollector->getTmpData();
            
            $this->annotationReader = new AnnotationReader(new Parser, new ArrayCache);
            
            if (empty($this->openApi)) {
                $this->openApi = app(OpenApi::class);
                Initializer::initializeData($this->openApi);
            }
//            $this->saveToFile();
        }
    }
    
    /**
     * Save data
     */
    public function saveToFile()
    {
        $this->dataCollector->saveTmpData($this->openApi->toArray());
        $this->dataCollector->saveData();
    }
    
    /**
     * @param  Request  $request
     * @param $response
     */
    public function addData($request, $response)
    {
        $this->request = request();
        $this->response = $response;
        
        $controllerAction = $this->request->route()->getActionName();
        if ($controllerAction === 'Closure') {
            //
        } else {
            $explodedController = explode('@', $controllerAction);
            
            $controllerClass = $explodedController[0];
            $controllerClass = new $controllerClass;
            $actionName = $explodedController[1];
            $requestClass = $this->getRequestClass($controllerClass, $actionName);
            if ($requestClass) {
                $requestClass = new $requestClass;
            }
            
            $actionInfo = $this->annotationReader->getMethodAnnotations($controllerClass,
                $actionName); // title, description, group
            $controllerInfo = $this->annotationReader->getClassAnnotations($controllerClass); // title, description, group
            $queryParameters = [];
            if ($requestClass) {
                $queryParameters = $this->annotationReader->getMethodAnnotations($requestClass,
                    'rules');  // array ['key' => 'description']
            }
            
            $url = "/{$this->getUri()}";
            $method = strtolower($this->request->getMethod());
            $security = $this->getSecurity();
            
            // set base path info
            $path = $this->openApi->getPath($url, $method);
            if ($path === null) {
                $tag = new Tag();
                $tag->setName($actionInfo->get('group', false) ?: $controllerInfo->get('group', 'other'));
                $tag->setDescription($actionInfo->get('groupDesc', false) ?: $controllerInfo->get('groupDesc'));
                
                $this->openApi->addTag($tag);
                
                $path = new Path();
                $path->setOperationId($url.'@'.$method);
                $path->setSummary($actionInfo->get('title', ''));
                $path->setDescription($actionInfo->get('description', ''));
                $path->addTag($tag->name);
                if ($security) {
                    $path->addSecurity($security['name'], $security['value']);
                }
            }
            
            // set path params
            $params = [];
            preg_match_all('/{.*?}/', $url, $params);
            $params = Arr::collapse($params);
            foreach ($params as $param) {
                $name = preg_replace('/[{}]/', '', $param);
                $value = Arr::get($request->route()->parameters, $name);
                if (isset($value->id)) {
                    $value = $value->id;
                }
                $schema = new Schema();
                $schema->setType('string');
                $schema->setExample($value);
                
                $path->addParameter('path', $name, $schema, '', true);
            }
    
            // set query params
            if ($method === 'get') {
                foreach ($queryParameters as $name => $desc) {
                    $value = $this->request->get($name);
                    $desc = $this->parsePropDesc($desc, $value);
                    $required = false;
                    $deprecated = strpos($desc['desc'], '(deprecated)') !== false;
        
                    $schema = new Schema();
                    $schema->setExample($value);
        
                    if ($desc['type'] === 'array') {
                        $schema->setType('array');
                        $schemaProp = new Schema();
                        $schemaProp->setType('string');
                        $schema->setItems($schemaProp);
                    } else {
                        $schema->setType('string');
                    }
        
                    $path->addParameter('query', $name, $schema, $desc['desc'], $required, $deprecated);
                }
            }
            
            // parse body
            if (!in_array($method, ['get', 'delete'])) {
                $requestBody = new RequestBody();
                $requestBody->setRequired(true);
                
                $schema = new Schema();
                $schema->setRef('requestBodies', $this->getSchemaFromResponse($requestClass));
                $requestBody->setContent($schema, $request->all());
                
                $path->setRequestBody($requestBody);
            }
            
    
            if ($method === 'get') {
                $path->setExternalDocs('https://docs.google.com/document/d/1hXa1XeIBHMpsW-3SuzK-QOtN7dxZimWXD_zKzToCP5Q/edit?usp=sharing', 'Документация к GET запросам');
            }
            
            // Parse response
            $code = $this->response->getStatusCode();
            $example = $this->response->getContent();
            $original = $this->response->original;
            $mediaType = $this->response->headers->get('Content-type');
            if (is_null($mediaType)) {
                $mediaType = 'text/plain';
            }
            
            if ($mediaType === 'application/json') {
                $example = json_decode($example, true);
            }
            
            $schema = $this->getSchemaFromData($example, $original);
            
            $pathResponse = new PathResponse();
            $pathResponse->addContent($schema, $example, $mediaType);
            
            $path->addResponse($code, $pathResponse);
            //
            
            $this->openApi->addPath($url, $method, $path);
        }
        
        $this->saveToFile();
        $this->dataCollector->saveTmpData($this->openApi);
    }
    
    public function getSchemaFromResponse($requestClass)
    {
        $parameters = $this->annotationReader->getMethodAnnotations($requestClass, 'rules');
        $info = $this->annotationReader->getClassAnnotations($requestClass);
    
        $description = $info->get('description', '');
        $requestBodyName = class_basename($requestClass);
        
        if ($this->openApi->components->getItem('requestBodies', $requestBodyName)) {
            return $requestBodyName;
        }
        
        
        $schema = new Schema();
        $schema->setType('object');
        $schema->setDescription($description);
        
        foreach ($parameters as $name => $desc) {
            $required = false;
            $desc = $this->parsePropDesc($desc);
            $types = [];
            preg_match_all('/([^{}]+)/', $desc['type'], $types);
            $types = $types[0];
            
            $propSchema = new Schema();
            $propSchema->setDescription($desc['desc']);
            
            if ($types[0] === 'array') {
                if (isset($types[1]) && class_exists($types[1])) {
                    $propSchema->setType('array');
                    $propSchema2 = new Schema();
                    $propSchema2->setRef('requestBodies', $this->getSchemaFromResponse(new $types[1]));
                    $propSchema->setItems($propSchema2);
                }
            } else if (class_exists($types[0]) && is_a($propsRequestClass = new $types[0], Request::class)) {
                $propSchema->setRef('requestBodies', $this->getSchemaFromResponse($propsRequestClass));
            } else {
                $rules = array_map(function($rule) use (&$required) {
                    $pieces = explode(':', $rule);
                    if ($pieces[0] === 'unique') {
                        return 'unique';
                    } else if ($rule === 'required') {
                        $required = true;
                    }
                    return $rule;
                }, Arr::get($requestClass->rules(), $name, []));
                $propSchema->setType($types[0]);
                $propSchema->setOther('rules', implode(', ', $rules));
            }
            
            $schema->setProperty($name, $propSchema, $required);
        }
        
        $this->openApi->components->setRequestBody($requestBodyName, $schema);
        
        return $requestBodyName;
    }
    
    /**
     * @param  mixed  $description
     * @param  string  $value
     * @return array
     * @throws \Exception
     */
    protected function parsePropDesc($description, $value = '')
    {
        $string = '{string}';
        if (is_string($description)) {
            $string = $description;
        } else if (is_array($description)) {
            throw new \Exception('Swagger generator: Find more than one prop description in resource');
        }
        
        $typeEnd = strPos($string, '}') - 1 ?: 0;
        
        if (!$typeEnd) {
            $type = gettype($value);
        } else {
            $type = substr($string, strPos($string, '{') + 1, $typeEnd) ?: 'string';
        }
        
        $description = trim(substr($string, $typeEnd + 3));
        
        return [
            'type' => $type,
            'desc' => $description,
        ];
    }
    
    public function getSchemaFromData($data, $original = null)
    {
        $schema = new Schema();
        
        $type = $original === null ? gettype($data) : gettype($original);
        if ($type === 'object' && is_a($original, JsonResource::class)) {
            $newModelName = $this->saveModel($original, $data);

            if ($original->isCollection()) {
                $schema->setType('array');
                $propSchema = new Schema();
                $propSchema->setRef('schemas', $newModelName);
                $schema->setItems($propSchema);
            } else {
                $schema->setRef('schemas', $newModelName);
            }
        } else {
            $type = gettype($data);
            
            if ($type === 'array') {
                if (Arr::isAssoc($data)) {
                    $schema->setType('object');
                    foreach ($data as $key => $value) {
                        $propSchema = $this->getSchemaFromData($value, Arr::get($original, $key));
                        $schema->setProperty($key, $propSchema);
                    }
                } else {
                    $schema->setType('array');
                    if (isset($data[0])) {
                        $propSchema = $this->getSchemaFromData($data[0], Arr::get($original, 0));
                        $schema->setItems($propSchema);
                    }
                }
            } else {
                $schema->setType($type);
            }
        }
        return $schema;
    }
    
    protected function saveModel(JsonResource $object, $data = [])
    {
        $class = get_class($object);
        $modelName = str_replace('Resource', '', Arr::last(explode('\\', $class)));
        $fields = $this->annotationReader->getMethodAnnotations($class, 'fields');
        $relations = $this->annotationReader->getMethodAnnotations($class, 'relations');
        $info = $this->annotationReader->getClassAnnotations($class);
    
        if ($this->openApi->components->getItem('schemas', $modelName)) {
            return $modelName;
        }
        
        if ($object->isCollection()) {
            $data = Arr::get($data, 'data.0');
        }

        $schema = new Schema();
        $schema->setType('object');
        $schema->setDescription($info->get('description'));
        $schema->setExample($data);
    
        foreach ($fields->toArray() as $key => $desc) {
            $value = Arr::get($data, $key, '');
            $desc = $this->parsePropDesc($desc, $value);
        
            $propSchema = new Schema();
            $propSchema->setType($desc['type']);
            $propSchema->setDescription($desc['desc']);
            $schema->setProperty($key, $propSchema);
        }
        
        $needToAdd = [];
    
        foreach ($relations->toArray() as $key => $desc) {
            if ($key === 'param' || $key === 'return') {
                continue;
            }
    
            $value = Arr::get($data, $key, '');
            $desc = $this->parsePropDesc($desc, $value);
            
            $types = [];
            preg_match_all('/([^{}]+)/', $desc['type'], $types);
            $types = $types[0];
    
            $propSchema = new Schema();
            $propSchema->setDescription($desc['desc']);
            
            if ($types[0] === 'array') {
                $class = Arr::get($types, 1, '');
                if (class_exists($class)) {
                    $className = $this->saveModel(new $class(), $value);
                    $propSchema->setType('array');
                    $propSchema2 = new Schema();
                    $propSchema2->setRef('schemas', $className);
                    $propSchema->setItems($propSchema2);
                }
            } else {
                $class = $types[0];
                if (class_exists($class)) {
                    $propModelName = str_replace('Resource', '', Arr::last(explode('\\', $class)));
                    $needToAdd[] = [
                        'class' => new $class(),
                        'value' => $value
                    ];
                    $propSchema->setRef('schemas', $propModelName);
                }
            }
            
            $schema->setProperty($key, $propSchema);
        }
    
        $this->openApi->components->setSchema($modelName, $schema);
    
        foreach ($needToAdd as $item) {
            $this->saveModel($item['class'], $item['value']);
        }
        
        return $modelName;
    }
    
    
    protected function getRequestClass($controller, $action)
    {
        $route = $this->request->route();
        
        $actionParameters = $this->resolveClassMethodDependencies($route->parametersWithoutNulls(), $controller,
            $action);
        return Arr::first($actionParameters, function ($key, $parameter) {
            return preg_match('/Request/', $key);
        });
    }
    
    protected function getUri()
    {
        $uri = $this->request->route()->uri();
        $basePath = preg_replace("/^\//", '', config('swagger.basePath'));
        $preparedUri = preg_replace("/^{$basePath}/", '', $uri);
        
        return preg_replace("/^\//", '', $preparedUri);
    }
    
    
    protected function getSecurity()
    {
        $header = '';
        $type = '';
        
        if (isset($this->openApi->components->securitySchemes['jwt'])) {
            $type = 'jwt';
            $header = $this->request->header('authorization');
        }
        if (!$header && isset($this->openApi->components->securitySchemes['laravel'])) {
            $type = 'laravel';
            $header = $this->request->cookie('__ym_uid');
        }
        
        if (!empty($header)) {
            return ['name' => $type, 'value' => [],];
        }
        
        return null;
    }

//
//    /**
//     * @return array
//     */
    protected function getPathParams()
    {
        $params = [];
        preg_match_all('/{.*?}/', $this->uri, $params);
        $params = Arr::collapse($params);
        
        $result = [];
        
        foreach ($params as $param) {
            $key = preg_replace('/[{}]/', '', $param);
            
            $result[] = [
                'in' => 'path',
                'name' => $key,
                'description' => '',
                'required' => true,
                'schema' => [
                    'type' => 'string',
                ],
            ];
        }
        
        return $result;
    }
//
//    /**
//     * Get uri of item.
//     *
//     * @return string
//     */
//
//
//    /**
//     * NOTE: All functions below are temporary solution for
//     * this issue: https://github.com/OAI/OpenAPI-Specification/issues/229
//     * We hope swagger developers will resolve this problem in next release of Swagger OpenAPI
//     *
//     * @param $parameters
//     * @param $types
//     * @param $example
//     */
//    protected function replaceNullValues($parameters, $types, &$example)
//    {
//        foreach ($parameters as $parameter => $value) {
//            if (is_null($value) && in_array($parameter, $types)) {
//                $example[$parameter] = $this->getDefaultValueByType($types[$parameter]['type']);
//            } elseif (is_array($value)) {
//                $this->replaceNullValues($value, $types, $example[$parameter]);
//            } else {
//                $example[$parameter] = $value;
//            }
//        }
//    }
//
//    /**
//     * @param $type
//     * @return mixed
//     */
//    protected function getDefaultValueByType($type)
//    {
//        $values = [
//            'object' => 'null',
//            'boolean' => false,
//            'date' => "0000-00-00",
//            'integer' => 0,
//            'string' => '',
//            'double' => 0
//        ];
//
//        return $values[$type];
//    }
    
    /*****************************************
     * parseResponse                         *
     *****************************************/
}
