<?php

namespace App\App\Models;


use App\System\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;


class Complex extends Model
{
    protected $table = 'app__complex';

    protected $attributes = [

    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'complexAdminId' => 'integer',
        'parkingSpaces' => 'integer',
        'phone' => 'integer',
        'email' => 'string',
        'instagram' => 'string',
        'whatsapp' => 'string',
        'address' => 'string',
        'createdAt' => 'datetime',
    ];

    /*************
     * Relations *
     *************/





    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return Complex
     */
    public static function create($data)
    {
        $model = new self();
        $model->title = Arr::get($data, 'title');
        $model->complexAdminId = Arr::get($data, 'complexAdminId');
        $model->parkingSpaces = Arr::get($data, 'parkingSpaces');
        $model->phone = Arr::get($data, 'phone');
        $model->email = Arr::get($data, 'email');
        $model->instagram = Arr::get($data, 'instagram');
        $model->whatsapp = Arr::get($data, 'whatsapp');
        $model->address = Arr::get($data, 'address');
        $model->save();

        return $model;
    }

    /**
     * @param array $data
     * @return Complex
     */
    public function edit($data)
    {
        $this->postFields([ 'title', 'complexAdminId', 'parkingSpaces', 'phone', 'email', 'instagram', 'whatsapp', 'address'], $data);
        $this->save();


        return $this;
    }

    public static function scopeByTitle(Builder $query, $value, $type = 'like')
    {
        return $query->where(function($query) use ($value) {
            return $query->whereRaw(
                'LOWER("app__complex"."title") LIKE ?',
                ['%' . mb_strtolower($value[0]) . '%']
            );
        });
    }


}
