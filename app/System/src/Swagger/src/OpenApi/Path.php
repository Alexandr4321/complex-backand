<?php


namespace App\System\Swagger\OpenApi;


/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#operationObject
 *
 * Class Path
 */
class Path
{
    public $operationId = '';
    
    public $summary = '';
    
    public $description = '';
    
    public $tags = [];
    
    public $parameters = [];
    
    public $responses = [];
    
    public $security = [];
    
    /** @var RequestBody */
    public $requestBody;
    
    public $deprecated = false;
    
    public $externalDocs = [];
    
    
    /**
     * @param string $operationId
     */
    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;
    }
    
    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }
    
    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @param RequestBody $body
     */
    public function setRequestBody(RequestBody $body)
    {
        $this->requestBody = $body;
    }
    
    /**
     * @param string $deprecated
     */
    public function setDeprecated($deprecated)
    {
        $this->deprecated = $deprecated;
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
     * @param string $name
     */
    public function addTag($name)
    {
        $this->tags[] = $name;
    }
    
    /**
     * @param string $in  Can be: query, path, cookie, header
     * @param string $name
     * @param Schema $schema
     * @param string $description
     * @param bool $required
     * @param bool $deprecated
     */
    public function addParameter($in, $name, Schema $schema, $description = null, $required = false, $deprecated = false)
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter['in'] === $in && $parameter['name'] === $name) {
                unset($parameter);
            }
        }
        
        $parameter = [
            'in' => $in,
            'name' => $name,
            'description' => $description,
            'required' => $required,
            'deprecated' => $deprecated,
            'schema' => $schema->toArray(),
        ];
        
        if ($description) {
            $parameter['description'] = $description;
        }
        
        $this->parameters[] = $parameter;
    }
    
    /**
     * @param string $code  Http code
     * @param PathResponse $response
     */
    public function addResponse($code, PathResponse $response)
    {
        $this->responses[$code] = $response->toArray();
    }
    
    /**
     * @param string $name  Example 'jwt'
     * @param array $value  Example []
     */
    public function addSecurity($name, $value)
    {
        if (array_search($name, array_column($this->security, 'name')) === false) {
            $this->security[] = [$name => $value];
        }
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'operationId' => $this->operationId,
            'tags' => $this->tags,
            'summary' => $this->summary,
            'description' => $this->description,
            'parameters' => $this->parameters,
            'responses' => $this->responses,
            'security' => $this->security,
            'deprecated' => $this->deprecated,
            'externalDocs' => $this->externalDocs,
        ];
        
        if ($this->requestBody) {
            $result['requestBody'] = $this->requestBody->toArray();
        }
        
        return $result;
    }
}
