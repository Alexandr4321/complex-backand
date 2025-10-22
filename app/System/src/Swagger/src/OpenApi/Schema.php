<?php


namespace App\System\Swagger\OpenApi;

/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#schemaObject
 *
 * Class Schema
 */
class Schema
{
    public $type = '';
    
    public $example = null;
    
    public $required = false;
    
    public $ref = [];
    
    /** @var array[Schema] */
    public $properties = [];
    
    public $description = null;
    
    /** @var Schema */
    public $items = null;
    
    public $externalDocs = [];
    
    public $other = [];
    
    
    /**
     * @param  string  $type  Can be integer, long, float, double, string, byte, binary, boolean, date, dateTime, password
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Used only if $type === 'array'
     * @param  Schema  $items
     */
    public function setItems(Schema $items)
    {
        $this->items = $items;
    }
    
    /**
     * @param string $url
     * @param string $description
     */
    public function setExternalDocs($url, $description = '')
    {
        $externalDocs = [
            'url' => $url,
        ];
        
        if ($description) {
            $externalDocs['description'] = $description;
        }
        
        $this->externalDocs = $externalDocs;
    }
    
    /**
     * @param  string  $name
     * @param  Schema  $schema
     * @param  bool  $required
     */
    public function setProperty($name, $schema, $required = false)
    {
        if ($this->type === 'object') {
            $this->properties[$name] = $schema;
            if ($required) {
                $this->required[] = $name;
            }
        }
    }
    
    /**
     * @param  mixed  $example
     */
    public function setExample($example)
    {
        $this->example = $example;
    }
    
    /**
     * @param  mixed  $description
     */
    public function setDescription($description)
    {
        if (is_string($description)) {
            $this->description = $description;
        }
    }
    
    /**
     * @param  string  $type  Can be: schemas, requestBodies
     * @param  string  $name
     */
    public function setRef($type, $name)
    {
        $this->ref = [
            'type' => $type,
            'name' => $name,
        ];
    }
    
    /**
     * @param string $name
     * @param string $value
     */
    public function setOther($name, $value)
    {
        $this->other[$name] = $value;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->ref) {
            $result = [
                '$ref' => '#/components/'.$this->ref['type'].'/'.$this->ref['name'],
            ];
        } else {
            $result = [
                'type' => $this->type,
            ];
        }
        
        if ($this->description) {
            $result['description'] = $this->description;
        }
        
        if ($this->required) {
            $result['required'] = $this->required;
        }
        
        if (!is_null($this->example)) {
            $result['example'] = $this->example;
        }
        
        if ($this->type === 'object') {
            if ($this->properties) {
                $result['properties'] = array_map(function ($i) {
                    return $i->toArray();
                }, $this->properties);
            }
            if ($this->required) {
                $result['required'] = $this->required;
            }
        }
        
        if ($this->type === 'array') {
            if ($this->items) {
                $result['items'] = $this->items->toArray();
            }
        }
    
        if (($this->externalDocs)) {
            $result['externalDocs'] = $this->externalDocs;
        }
    
        if (($this->other)) {
            foreach ($this->other as $name => $value) {
                $result[$name] = $value;
            }
        }
        
        return $result;
    }
}
