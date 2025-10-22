<?php

namespace App\Auth\Models;

use App\Auth\Types\TypeRoleCreate;
use App\Base\Locale\Localized;
use App\System\Models\Model;

/**
 *
 */
class Role extends Model
{
    use Localized;
    
    protected $table = 'auth__roles';
    
    protected $attributes = [
        'modelType' => null,
        'description' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'modelType' => 'string',
        'createdAt' => 'datetime',
    ];
    
    public const localized = [
        'description',
    ];
    
    public const max = [
        'name' => 31,
        'modelType' => 31,
        'description' => 511,
    ];
    
    
    /*************
     * Relations *
     *************/
    
    public function permits()
    {
        return $this->belongsToMany(Permit::class, table(RolePermit::class), 'roleId', 'permitId')
            ->using(RolePermit::class);
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  TypeRoleCreate  $data
     * @return Role
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
     * @return bool|null
     */
    public function delete()
    {
        // todo отвязать permits
        
        return parent::delete();
    }
}
