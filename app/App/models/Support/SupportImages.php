<?php

namespace App\App\Models;

use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Models\Pivot;
use Illuminate\Support\Arr;

class SupportImages extends Pivot
{
    protected $table = 'app__support_images';

    protected $attributes = [
    ];

    protected $casts = [
        'id' => 'integer',
        'supportId' => 'integer',
        'userId' => 'integer',
        'fileId' => 'integer',
        'createdAt' => 'datetime',
    ];

    public const max = [
    ];


    /*************
     * Relations *
     *************/

    public function news()
    {
        return $this->belongsTo(Support::class, 'supportId');
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
     * @return SupportImages
     */
    public static function create($data)
    {
        $model = new self();
        $model->supportId = $data['supportId'];
        $model->userId = $data['userId'];
        $model->fileId = $data['fileId'];
        $model->save();

        return $model;
    }
}
