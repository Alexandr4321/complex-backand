<?php

namespace App\App\Models;

use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Models\Pivot;
use Illuminate\Support\Arr;

class HomeImages extends Pivot
{
    protected $table = 'app__home_images';

    protected $attributes = [
    ];

    protected $casts = [
        'id' => 'integer',
        'homeId' => 'integer',
        'userId' => 'integer',
        'fileId' => 'integer',
        'createdAt' => 'datetime',
    ];

    public const max = [
    ];


    /*************
     * Relations *
     *************/

    public function home()
    {
        return $this->belongsTo(Home::class, 'homeId');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'fileId');
    }


    /**************
     * Operations *
     **************/

    /**
     * @param  array  $data
     * @return HomeImages
     */
    public static function create($data)
    {
        $model = new self();
        $model->homeId = $data['homeId'];
        $model->userId = $data['userId'];
        $model->fileId = $data['fileId'];
        $model->save();

        return $model;
    }
}
