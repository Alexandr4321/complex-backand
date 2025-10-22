<?php

namespace App\Base\Models;

use App\System\Models\Model;
use Illuminate\Support\Arr;

class Translation extends Model
{
    protected $table = 'base__translations';
    
    protected $attributes = [
        'ownerId' => null,
        'ownerType' => null,
        'tagId' => null,
        'field' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'localeId' => 'integer',
        'ownerId' => 'integer',
        'ownerType' => 'string',
        'tagId' => 'string', // индекс из Translation::$tags, либо используется owner либо tag
        'field' => 'string',
        'value' => 'string',
        'createdAt' => 'datetime',
    ];
    
    public const max = [
        'field' => 127,
        'value' => 1000,
    ];
    
    /**
     * В базе данных хранится индекс из этого массива как ключ тэга
     */
    public const tags = [
        'interface', 'content',
    ];
    
    
    /*************
     * Relations *
     *************/
    
    public function owner()
    {
        return $this->morphTo(null, 'ownerType', 'ownerId');
    }
    
    public function locale()
    {
        return $this->belongsTo(Locale::class, 'localeId');
    }
    
    
    /**************
     * Attributes *
     **************/
    
    public function getTagAttribute()
    {
        return Arr::get(Translation::tags, $this->tagId);
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *   'value' => string,
     *   'localeId' => Locale::$id,
     *   'owner' => Model, // default null
     *   'tagName' => Translation::$tags, // default null
     *   'field' => string, // default null
     * ];
     * </code>
     *
     * @return Translation
     */
    public static function create($data)
    {
        $model = new self();
        
        $model->value = $data['value'];
        $model->localeId = $data['localeId'];
        if ($owner = Arr::get($data, 'owner')) {
            $model->owner()->associate($owner);
        }
        if ($tagName = Arr::get($data, 'tagName')) {
            $model->tagId = array_search($tagName, Translation::tags);
        }
        $model->postFields([ 'field' ], $data);
        $model->save();
        
        return $model;
    }
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *   'value' => string,
     * ];
     * </code>
     */
    public function edit($data)
    {
        $this->postFields([ 'value' ], $data);
        $this->save();
    }
}
