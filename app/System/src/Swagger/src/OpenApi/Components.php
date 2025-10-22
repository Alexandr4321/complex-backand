<?php

namespace App\System\Swagger\OpenApi;

use Illuminate\Support\Arr;

/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#componentsObject
 *
 * Class Components
 */
class Components
{
    public $schemas = [];
    
    public $securitySchemes = [];
    
    public $requestBodies = [];
    
    
    /**
     * @param string $type  Can be: schemas, securitySchemes, requestBodies
     * @param string $name
     * @return Schema
     */
    public function getItem($type, $name)
    {
        if ($this->{$type}) {
            return Arr::get($this->{$type}, $name);
        }
        
        return null;
    }
    
    /**
     * @param string $name
     * @param Schema $schema
     */
    public function setSchema($name, Schema $schema)
    {
        $this->schemas[$name] = $schema;
    }
    
    /**
     * @param string $name
     * @param array $value
     */
    public function setSecuritySchema($name, $value)
    {
        $this->securitySchemes[$name] = $value;
    }
    
    /**
     * @param string $name
     * @param Schema $schema
     */
    public function setRequestBody($name, Schema $schema)
    {
        $this->requestBodies[$name] = $schema;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
    
        if ($this->schemas) {
            $result['schemas'] = array_map(function ($i) { return $i->toArray(); }, $this->schemas);
        }
    
        if ($this->securitySchemes) {
            $result['securitySchemes'] = $this->securitySchemes;
        }
    
        if ($this->requestBodies) {
            $result['requestBodies'] = array_map(function ($i) { return $i->toArray(); }, $this->requestBodies);
        }
        
        return $result;
    }
}
