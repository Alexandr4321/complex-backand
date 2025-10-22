<?php

namespace App\App\Models;

use App\System\Models\Model;
use Illuminate\Support\Arr;

class GuestAuto extends Model
{
    protected $table = 'app__guest_auto';

    protected $attributes = [

    ];

    protected $casts = [
        'id' => 'integer',
        'userId' => 'integer',
        'number' => 'string',
        'brand' => 'string',
        'status' => 'string',
        'phone' => 'string',
        'createdAt' => 'datetime',
    ];


    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return GuestAuto
     */
    public static function create($data)
    {
        $model = new self();
        $model->userId = Arr::get($data, 'userId');
        $model->number = mb_strtoupper(Arr::get($data, 'number'));
        $model->brand = Arr::get($data, 'brand');
        $model->phone = Arr::get($data, 'phone');
        $model->status = Arr::get($data, 'status');
        $model->save();

        return $model;
    }

    /**
     * @param array $data
     * @return GuestAuto
     */
    public function edit($data)
    {
        if (isset($data['number'])) {
            $data['number'] = mb_strtoupper($data['number']);
        }
        $this->postFields([
            'userId', 'number', 'brand', 'status', 'phone'], $data);
        $this->save();


        return $this;
    }








}
