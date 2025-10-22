<?php

namespace App\Auth\Models;

use App\Auth\Types\TypeAccessCreate;
use App\Company\Models\Company;
use App\System\Models\Model;

/**
 * Назначенное полномочие
 */
class Access extends Model
{
    protected $table = 'auth__accesses';
    
    protected $attributes = [
        'contractorId' => null,
        'contractorType' => null,
        'modelId' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'name' => 'string', // назначенное полномочие
        'type' => 'enum', // values from self::types
        'modelId' => 'integer', // используется если полномочие назначили на конкретный объект
        'ownerId' => 'integer', // на кого назначили
        'ownerType' => 'string', // values from self::ownerTypes
        'contractorId' => 'integer', // кто назначил
        'contractorType' => 'string', // values from self::contractorTypes
        'createdAt' => 'datetime',
    ];
    
    public const max = [
        'ownerType' => 63,
        'contractorType' => 63,
        'name' => 31,
    ];
    
    public const types = [
        'permit' => Permit::class,
        'role' => Role::class,
    ];
    
    public const ownerTypes = [
        'user' => User::class,
        'company' => Company::class,
    ];
    
    public const contractorTypes = [
        'user' => User::class,
        'company' => Company::class,
    ];
    
    
    /*************
     * Relations *
     *************/
    
    public function owner()
    {
        return $this->morphTo(null, 'ownerType', 'ownerId');
    }
    
    public function contractor()
    {
        return $this->morphTo(null, 'contractorType', 'contractorId');
    }
    
    public function precisions()
    {
        return $this->hasMany(AccessPrecision::class, 'accessId');
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  TypeAccessCreate  $data
     * @return Access
     */
    public static function create($data)
    {
        $model = new self();
        
        $model->name = $data->access->name;
        $model->type = get_class($data->access);
        $model->modelId = $data->modelId;
        $model->owner()->associate($data->owner);
        $model->contractor()->associate($data->contractor);
        $model->save();
        
        foreach ($data->precisions as $precision) {
            $model->precisions()->save(new AccessPrecision($precision->toArray()));
        }
        
        return $model;
    }
    
    /**
     * @return bool|null
     */
    public function delete()
    {
        $this->deleteRelationList('data');
        
        return parent::delete();
    }
}
