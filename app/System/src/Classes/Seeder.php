<?php

namespace App\System\Classes;

use App\System\Exceptions\ServerException;
use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Http\UploadedFile;
use Spatie\SimpleExcel\SimpleExcelReader;

class Seeder extends BaseSeeder
{
    protected $filePath = 'files/';
    
    protected function getPath($name = '')
    {
        return database_path($this->filePath.$name);
    }
    
    /**
     * @param  string  $name
     * @return array
     */
    protected function loadExcel($name)
    {
        return SimpleExcelReader::create($this->getPath($name))->getRows()->toArray();
    }
    
    /**
     * @param  string  $name
     * @return array
     * @throws ServerException
     */
    protected function loadJson($name)
    {
        $string = $this->loadFile($name);
        if ($string === false) {
            throw new ServerException("JSON file $name not found");
        }
        
        $json = json_decode($string, true);
        if ($json === null) {
            throw new ServerException("Can't decode JSON file $name");
        }
        
        return $json;
    }
    
    /**
     * @param  string  $name
     * @return false|string
     */
    protected function loadFile($name)
    {
        return file_get_contents($this->getPath($name));
    }
    
    /**
     * @param  string  $name
     * @param  string  $originalName
     * @return false|string
     */
    protected function uploadedFile($name, $originalName = null)
    {
        if (!$originalName) {
            $parts = explode('/', $name);
            $originalName = $parts[count($parts) - 1];
        }
        return new UploadedFile($this->getPath($name), $originalName);
    }
}
