<?php


namespace App\System\Swagger\OpenApi;

/**
 * Теги (Контроллеры). Не обязательно использовать.
 * Порядок в массиве задает сортировку.
 * Так же помогает сократить размер файла, если в роутах указывать только имя.
 *
 *
 * Class Tag
 */
class Tag
{
    public $name = '';
    
    public $description = '';
    
    public $externalDocs = [];
    
    
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return array
     */
    public function toArray()
    {
        $result = [
            'name' => $this->name,
        ];
    
        if ($this->description) {
            $result['description'] = $this->description;
        }
        if ($this->externalDocs) {
            $result['externalDocs'] = $this->externalDocs;
        }
        
        return $result;
    }
}
