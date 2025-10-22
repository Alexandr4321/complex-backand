<?php

namespace App\System\Resources;

class ResourceScope
{
    public const types = [
        'boolean', 'integer', 'float', 'string', 'datetime'
    ];
    
    protected $type;
    
    protected $name;
    
    
    public function __construct($type, $name = null)
    {
        $this->type = $type;
        $this->name = $name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getName($default)
    {
        return $this->name ?? $default;
    }
    
    public function call($query, $values, $filterType, $defaultName)
    {
        $scopeName = $this->getName($defaultName);
        if ($this->type === 'boolean') {
            $query->$scopeName($values[0]);
        } elseif(in_array($this->type, [ 'integer', 'float', 'string', 'datetime', ])) {
            $query->$scopeName($values, $filterType);
        }
    }
}
