<?php

namespace App\App\Models;

use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Models\Pivot;
use Illuminate\Support\Arr;

class NewsImages extends Pivot
{
    protected $table = 'app__news_images';

    protected $attributes = [
    ];

    protected $casts = [
        'id' => 'integer',
        'newsId' => 'integer',
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
        return $this->belongsTo(News::class, 'newsId');
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
     * @return NewsImages
     */
    public static function create($data)
    {
        $model = new self();
        $model->newsId = $data['newsId'];
        $model->userId = $data['userId'];
        $model->fileId = $data['fileId'];
        $model->save();

        return $model;
    }
}
