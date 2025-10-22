<?php

namespace App\Auth\Models;

use App\Auth\Types\TypeGrant;
use App\System\Models\Model;

/**
 * Минимальная единица полномочий, по которой определяется доступ к апи или интерфейсу.
 * Создаются разработчиком. Входят в состав Permit.
 * Эту сущность пользователь редактировать не может, так как эти имена используются в коде.
 * Примеры: product_read, product_edit
 */
class Grant extends Model
{
    protected $table = 'auth__grants';
    
    public const CREATED_AT = null;
    
    protected $attributes = [
        'modelType' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'modelType' => 'string'
    ];
    
    public const max = [
        'name' => 31,
        'modelType' => 31,
    ];
    
    /*************
     * Relations *
     *************/
    
    public function permits()
    {
        return $this->belongsToMany(Permit::class, table(PermitGrant::class), 'grantId', 'permitId')
            ->using(PermitGrant::class)
            ->withPivot([ 'isGlobal', ]);
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  TypeGrant  $data
     * @return Grant
     */
    public static function create($data)
    {
        $model = new self();
        
        $model->name = $data->name;
        $model->modelType = $data->modelType;
        $model->save();
        
        return $model;
    }
}
