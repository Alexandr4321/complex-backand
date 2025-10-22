<?php

namespace App\Base\Locale;

use App\Base\Models\Locale;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LocaleService
{
    private static $current;
    private static $def;
    
    /**
     * Получить текущую локаль
     *
     * @return Builder|Model
     */
    public static function current()
    {
        if (!self::$current) {
            $key = request('lang');
            if (!$key) $key = request()->header('Locale');
            if (!$key) $key = 'ru';
            self::$current = Locale::query()->where('name', $key)->firstOrFail();
        }
        return self::$current;
    }
    
    /**
     * Получить дефолтную локаль
     *
     * @return Builder|Model
     */
    public static function def()
    {
        if (!self::$def) {
            self::$def = Locale::query()->where('name', 'ru')->firstOrFail();
        }
        return self::$def;
    }
    
    /**
     * Создать таблицу переводов (используется в миграциях)
     *
     * @param  string  $modelClass
     */
    public static function createTable($modelClass)
    {
        $model = new $modelClass;
        Schema::create(self::getTranslationsTable($model), function (Blueprint $table) use ($model, $modelClass) {
            $table->integer('id');
            $table->integer('localeId')->unsigned();
            $table->primary([ 'id', 'localeId' ]);
            
            foreach ($model::localized as $fieldName) {
                $table->string($fieldName, $modelClass::getMax('title'))->nullable();
            }
        });
    }
    
    /**
     * Удалить таблицу переводов (используется в миграциях)
     *
     * @param  string  $modelClass
     */
    public static function dropTable($modelClass)
    {
        Schema::dropIfExists(self::getTranslationsTable(new $modelClass));
    }
    
    /**
     * Получить имя таблицы переводов для модели
     *
     * @param  Model  $model
     * @return string
     */
    public static function getTranslationsTable($model)
    {
        return $model->getTable().(is_string($model::localizedTable) ? $model::localizedTable : '_localized');
    }
}
