<?php

namespace App\Base\Models;

use App\System\Models\Model;
use Illuminate\Support\Arr;

class Locale extends Model
{
    protected $table = 'base__locales';
    
    protected $attributes = [
        'isHidden' => false,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'name' => 'string', // уникальное поле
        'title' => 'string',
        'isHidden' => 'boolean',
    ];
    
    
    /*************
     * Relations *
     *************/
    
    public function translations()
    {
        return $this->hasMany(Translation::class, 'localeId');
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *   'name' => string,
     *   'title' => string,
     *   'isHidden' => boolean, // default false
     * ];
     * </code>
     *
     * @return Locale
     */
    public static function create($data = [])
    {
        $model = new self();
        
        $model->name = $data['name'];
        $model->title = $data['title'];
        $model->postFields([ 'isHidden' ], $data);
        $model->save();
        
        return $model;
    }
    
    /**
     * @param  array  $data
     * <code>
     * $data = [
     *   'name' => string,
     *   'title' => string,
     *   'isHidden' => boolean,
     * ];
     * </code>
     */
    public function edit($data)
    {
        $this->postFields([ 'name', 'title', 'isHidden' ], $data);
        $this->save();
    }
}
