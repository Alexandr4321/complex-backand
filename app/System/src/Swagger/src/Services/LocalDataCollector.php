<?php

namespace App\System\Swagger\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class LocalDataCollector
{
    
    /**
     * @var  string  Path to api file.
     */
    public $filePath;
    
    /**
     * @var  array  Api array.
     */
    protected static $data;
    
    /**
     * LocalDataCollector constructor.
     *
     * @throws FileNotFoundException
     */
    public function __construct()
    {
        $this->filePath = config('swagger.apiFilePath', storage_path('swagger/api.json'));
    
        if (empty($this->filePath)) {
            throw new FileNotFoundException('Wrong file path for swagger api.json: '.$this->filePath);
        }
    }
    
    /**
     * @param $tempData
     */
    public function saveTmpData($tempData) {
        self::$data = $tempData;
    }
    
    /**
     * @return mixed
     */
    public function getTmpData()
    {
        return self::$data;
    }
    
    /**
     *
     */
    public function saveData()
    {
        $content = json_encode(self::$data);

        file_put_contents($this->filePath, $content);

        self::$data = [];
    }
    
    /**
     * @return mixed
     * @throws FileNotFoundException
     */
    public function getDocumentation()
    {
        if (!file_exists($this->filePath)) {
            throw new FileNotFoundException();
        }

        $fileContent = file_get_contents($this->filePath);

        return json_decode($fileContent);
    }
}
