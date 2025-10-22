<?php

namespace App\App\Models;

use App\System\Models\Model;
use Illuminate\Support\Arr;

class Apartment extends Model
{
    protected $table = 'app__apartment';

    protected $attributes = [

    ];

    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'number' => 'integer',
        'apartmentArea' => 'float',
        'createdAt' => 'datetime',
    ];


    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return Apartment
     */
    public static function create($data)
    {
        $model = new self();
        $model->userId = Arr::get($data, 'userId');
        $model->number = Arr::get($data, 'number');
        $model->apartmentArea = Arr::get($data, 'apartmentArea');
        $model->save();

        return $model;
    }

    /**
     * @param array $data
     * @return Apartment
     */
    public function edit($data)
    {
        $this->postFields([
            'userId', 'number', 'apartmentArea'], $data);
        $this->save();


        return $this;
    }








}
