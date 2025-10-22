<?php

namespace App\System\Resources;

class ResourceQuery
{
    
    protected $queryCallback;
    
    protected $callback;
    
    
    public function __construct($queryCallback, $callback = '')
    {
        $this->queryCallback = $queryCallback;
        $this->callback = $callback;
    }
    
    public function query($query)
    {
        $callback = $this->queryCallback;
        return $callback($query);
    }
    
    public function call($query)
    {
        if (!$this->callback) {
            return null;
        }
        
        $callback = $this->callback;
        return $callback($query);
    }
}
