<?php


namespace App\System\Swagger\OpenApi;

/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#responseObject
 *
 * Class PathResponse
 */
class PathResponse
{
    protected $description = '';
    
    protected $headers = [];
    
    protected $content = [];
    
    protected $links = [];
    
    
    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @param string $name
     * @param string $description
     * @param string $type  Can be: string, integer
     * @param bool $required
     * @param bool $deprecated
     */
    public function addHeader($name, $description, $type, $required = false, $deprecated = false)
    {
        $this->headers[$name] = [
            'description' => $description,
            'required' => $required,
            'deprecated' => $deprecated,
            'schema' => [
                'type' => $type,
            ],
        ];
    }
    
    /**
     * @param string $mediaType
     * @param Schema $schema
     * @param mixed $example
     * @param array $encoding  https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#encodingObject
     */
    public function addContent(Schema $schema, $example = null, $mediaType = 'application/json', $encoding = [])
    {
        $content = [
            'schema' => $schema->toArray(),
        ];
    
        if ($example) {
            $content['example'] = $example;
        }
        if ($encoding) {
            $content['encoding'] = $encoding;
        }
        
        $this->content[$mediaType] = $content;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [
            'content' => $this->content,
        ];
    
        if ($this->description) {
            $result['description'] = $this->description;
        }
        if ($this->headers) {
            $result['headers'] = $this->headers;
        }
        if ($this->links) {
            $result['links'] = $this->links;
        }
        
        return $result;
    }
}
