<?php

namespace App\System\Resources;

class Relation
{
    
    protected $resource;
    
    protected $callback;
    
    protected $name;
    
    
    public function __construct($resource, $callback = null, $name = null)
    {
        $this->resource = $resource;
        $this->callback = $callback;
        $this->name = $name;
    }
    
    public function getCallback()
    {
        return $this->callback;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getName($default)
    {
        return $this->name ?? $default;
    }
}
