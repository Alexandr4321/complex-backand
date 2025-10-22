<?php


namespace App\System\Swagger\OpenApi;

/**
 * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#requestBodyObject
 *
 * Теги (Контроллеры). Не обязательно использовать.
 * Порядок в массиве задает сортировку.
 * Так же помогает сократить размер файла, если в роутах указывать только имя.
 *
 * Class RequestBody
 */
class RequestBody
{
    
    public $description = '';
    
    public $required = false;
    
    public $content = [];
    
    
    /**
     * @param Schema $schema
     * @param string $example
     * @param string $contentType
     */
    public function setContent(Schema $schema, $example = '', $contentType = 'application/json')
    {
        $result = [
            'schema' => $schema->toArray(),
        ];
        
        if ($example) {
            $result['example'] = $example;
        }
    
        $this->content[$contentType] = $result;
    }
    
    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
    
        if ($this->description) {
            $result['description'] = $this->description;
        }
        if ($this->required) {
            $result['required'] = $this->required;
        }
        if ($this->content) {
            $result['content'] = $this->content;
        }
        
        return $result;
    }
}
