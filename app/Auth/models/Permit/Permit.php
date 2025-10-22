<?php

namespace App\Auth\Models;

use App\Auth\Types\TypePermitEdit;
use App\Base\Locale\Localized;
use App\Auth\Types\TypePermit;
use App\System\Models\Model;

/**
 * Permit это неделимая еденица бизнес полномочия.
 * Permit состоит из набора Grant`ов и создается программистом, список Permit`ов составляет проектный менеджер.
 * Пользователь может назначать Permit, но не может видеть из чего он состоит.
 */
class Permit extends Model
{
    use Localized;
    
    protected $table = 'auth__permits';
    
    public const CREATED_AT = null;

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'modelType' => 'string',
    ];
    
    public const max = [
        'name' => 31,
        'description' => 255,
    ];
    
    public const localized = [ 'description', ];
    
    
    /*************
     * Relations *
     *************/
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, table(RolePermit::class), 'permitId', 'roleId')
            ->using(RolePermit::class);
    }
    
    public function grants()
    {
        return $this->belongsToMany(Grant::class, table(PermitGrant::class), 'permitId', 'grantId')
            ->using(PermitGrant::class)
            ->withPivot([ 'isGlobal', ]);
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  TypePermit  $data
     * @return Permit
     */
    public static function create($data)
    {
        $model = new self();

        $model->name = $data->name;
        $model->modelType = $data->modelType;
        $model->description = $data->description;
        $model->save();

        return $model;
    }
    
    /**
     * @param  TypePermitEdit  $data
     * @return Permit
     */
    public function edit($data)
    {
        $this->description = $data->description;
        $this->save();
        
        return $this;
    }
}
