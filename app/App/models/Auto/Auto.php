<?php

namespace App\App\Models;

use App\System\Models\Model;
use Illuminate\Support\Arr;

class Auto extends Model
{
    protected $table = 'app__auto';

    protected $attributes = [

    ];

    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'number' => 'string',
        'brand' => 'string',
        'parkingSpace' => 'string',
        'createdAt' => 'datetime',
    ];


    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return Auto
     */
    public static function create($data)
    {
        $user = auth()->user();

        $model = new self();
        $model->userId = $user->id;
        $model->number = mb_strtoupper(Arr::get($data, 'number'));
        $model->brand = Arr::get($data, 'brand');
        $model->parkingSpace = Arr::get($data, 'parkingSpace');

        $model->save();

        return $model;
    }

    /**
     * @param array $data
     * @return Auto
     */
    public function edit($data)
    {
        if (isset($data['number'])) {
            $data['number'] = mb_strtoupper($data['number']);
        }
        $this->postFields([
            'userId', 'number', 'brand' , 'parkingSpace'], $data);
        $this->save();


        return $this;
    }








}
