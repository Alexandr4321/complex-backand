<?php

namespace App\System\Swagger\OpenApi;

use Illuminate\Support\Arr;

/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md
 *
 * Class OpenApi
 */
class OpenApi
{
    public $version = '3.0.0';
    
    public $info = [];
    
    public $paths = [];
    
    public $servers = [];
    
    public $security = [];
    
    public $tags = [];
    
    public $externalDocs = [];
    
    /** @var Components  */
    public $components;
    
    
    /**
     * OpenApi constructor.
     */
    public function __construct()
    {
        $this->components = new Components();
    }
    
    /**
     * @param OpenApiInfo $info
     */
    public function setInfo(OpenApiInfo $info)
    {
        $this->info = $info->toArray();
    }
    
    /**
     * @param string $url  Example: "/api/v1/sample/{id}"
     * @param string $type  Can be: get, put, post, delete, options, head, patch, trace
     * @param Path $path
     */
    public function addPath($url, $type, Path $path)
    {
        $this->paths[$url][$type] = $path;
    }
    
    /**
     * @param string $url
     * @param string $type
     * @return Path
     */
    public function getPath($url, $type)
    {
        return Arr::get($this->paths, $url.'.'.$type);
    }
    
    /**
     * @param string $url
     * @param string $description
     */
    public function addServer($url, $description = '')
    {
        $server = [
            'url' => $url,
        ];
        
        if ($description) {
            $server['description'] = $description;
        }
        
        $this->servers[] = $server;
    }
    
    /**
     * Global security for all routes.
     *
     * @param string $name
     * @param array $value
     */
    public function addSecurity($name, $value = [])
    {
        $this->security[$name] = $value;
    }
    
    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        if (array_search($tag->name, array_column($this->tags, 'name')) === false) {
            $this->tags[] = $tag->toArray();
        }
    }
    
    /**
     * External docs link appears after description.
     *
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
     * @return array
     */
    public function toArray()
    {
        $result = [
            'openapi' => $this->version,
        ];
        
        if ($this->info) {
            $result['info'] = $this->info;
        }
        if ($this->paths) {
            $result['paths'] = array_map(function ($paths) {
                return array_map(function ($path) {
                    return $path->toArray();
                }, $paths);
            }, $this->paths);
        }
        if ($this->servers) {
            $result['servers'] = $this->servers;
        }
        if ($this->security) {
            $result['security'] = $this->security;
        }
        if ($this->tags) {
            $result['tags'] = $this->tags;
        }
        if ($this->externalDocs) {
            $result['externalDocs'] = $this->externalDocs;
        }
        
        $components = $this->components->toArray();
        if ($components) {
            $result['components'] = $components;
        }
        
        return $result;
    }
}
