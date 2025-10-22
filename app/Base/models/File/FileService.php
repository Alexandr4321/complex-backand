<?php

namespace App\Base\Services;

use App\Base\Models\File;
use App\System\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Получить путь для атрибута $file->src для сохранения в бд
     * @param  File|Builder  $model
     * @param  string  $name  Имя физического файла
     * @return string
     */
    public static function srcPath($model, $name)
    {
        return $model->id.'/'.$name;
    }
    
    /**
     * Получить путь физического хранения файла
     * @param  string  $src  Путь из атрибута $file->src
     * @return string
     */
    public static function storagePath($src)
    {
        return 'files/'.$src;
    }
    
    /**
     * Получить содержимое файла
     * @param  string  $src  Путь из атрибута $file->src
     * @return string
     */
    public static function getContent($src)
    {
        return Storage::get(self::storagePath($src));
    }
    
    /**
     * Получить публичный путь файла для вставки в <img src="" />
     * @param  string  $src  Путь из атрибута $file->src
     * @return string
     */
    public static function publicPath($src)
    {
        $src = trim($src, '/');
        if ($src && strpos($src, 'http') !== 0) {
            return config('app.url').'/files/'.$src;
        }
        
        return $src;
    }
    
    /**
     * Прикрепить файлы к модели
     * @param  Model  $owner
     * @param  integer[]|File[]  $files  Массив Id файлов или массив моделей файлов
     * @param  callable  $callback  Функция вызывается после изменения каждого файла
     * @return File[]
     */
    public static function setFilesOwner($owner, $files, $callback = null)
    {
        $result = [];
        foreach ($files as $fileId) {
            $file = is_a($fileId, File::class) ? $fileId : File::query()->findOrFail($fileId);
            $file->owner()->associate($owner);
            $file->save();
            $result[] = $file;
            if ($callback) $callback($file);
        }
        
        return $result;
    }
    
    /**
     * Удаление файла или рекурсивное удаление директории
     * @param  string  $dir
     */
    public static function removeFileOrDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $name = $dir.DIRECTORY_SEPARATOR.$object;
                    if (is_dir($name) && !is_link($name)) {
                        self::removeFileOrDir($name);
                    } else {
                        unlink($name);
                    }
                }
            }
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }
    
    /**
     * @param  array  $data
     * @param  string  $key
     * @param  string  $model
     * @return File
     */
    public static function getFileFromData($data, $key, $model = 'App\Base\Models\File')
    {
        if ($file = Arr::get($data, $key, null)) {
            if (is_int($file)) {
                /** @var Model $model */
                $file = $model::query()->findOrFail($file);
            }
        }
        
        return $file;
    }
}
