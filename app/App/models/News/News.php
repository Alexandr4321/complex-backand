<?php

namespace App\App\Models;

use App\App\Resources\UnderCategoryResource;
use App\Auth\Models\User;
use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Exceptions\BusinessException;
use App\System\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'app__news';

    protected $attributes = [

    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'content' => 'string',
        'complexId' => 'integer',
        'active' => 'boolean',
        'createdAt' => 'datetime',
    ];






    /**************
     * Attributes *
     **************/

    public function getImageAttribute()
    {
        return FileService::publicPath($this->imageSrc);
    }

    public function getPhotoAttribute()
    {
        return FileService::publicPath($this->photoSrc);
    }


    /*************
     * Relations *
     *************/

    public function imageFile()
    {
        return $this->morphOne(File::class, null, 'ownerType', 'ownerId')
            ->where('tag', 'image');
    }

    public function photoFile()
    {
        return $this->morphOne(File::class, null, 'ownerType', 'ownerId')
            ->where('tag', 'photo');
    }



    public function images()
    {
        return $this->hasManyThrough(File::class, NewsImages::class, 'newsId', 'id', 'id', 'fileId');
    }






    /**************
     * Operations *
     **************/

    /**
     * @param array $data
     * @return News
     */
    public static function create($data)
    {
        $user = auth()->user();

        $model = new self();
        $model->complexId = Arr::get($data, 'complexId');
        $model->title = Arr::get($data, 'title');
        $model->content = Arr::get($data, 'content');
        $model->active = Arr::get($data, 'active' , false);
        $model->save();

        if (Arr::get($data, 'photos')) {
            foreach ($data['photos'] as $photo) {
                NewsImages::create([
                    'newsId' => $model->id,
                    'userId' => $user->id,
                    'fileId' => $photo,

                ]);
            }
        }

        return $model;
    }

    /**
     * @param array $data
     * @return News
     */
    public function edit($data)
    {
        $this->postFields([
            'title', 'content', 'complexId', 'active'
        ], $data);
        $this->save();

        if (Arr::get($data, 'photos')) {
            NewsImages::query()->where('newsId', $this->id);
            foreach ($data['photos'] as $photo) {
                NewsImages::create([
                    'newsId' => $this->id,
                    'userId' => auth()->user()->id,
                    'fileId' => $photo,
                ]);
            }
        }

        if (Arr::get($data, 'image')) {
            $file = $this->imageFile;
            if ($file && $file->id !== $data['image']) {
                $file->delete();
                $self = $this;
                FileService::setFilesOwner($this, [$data['image'],], function ($file) use ($self) {
                    $self->imageSrc = $file->src;
                    $self->save();
                    $file->tag = 'image';
                    $file->save();
                });
            }
            if (!$file) {
                $self = $this;
                FileService::setFilesOwner($this, [$data['image'],], function ($file) use ($self) {
                    $self->imageSrc = $file->src;
                    $self->save();
                    $file->tag = 'image';
                    $file->save();
                });
            }
        }
        if (Arr::get($data, 'photo')) {
            $file = $this->photoFile;
            if ($file && $file->id !== $data['photo']) {
                $file->delete();
                $self = $this;
                FileService::setFilesOwner($this, [$data['photo'],], function ($file) use ($self) {
                    $self->photoSrc = $file->src;
                    $self->save();
                    $file->tag = 'photo';
                    $file->save();
                });
            }
            if (!$file) {
                $self = $this;
                FileService::setFilesOwner($this, [$data['photo'],], function ($file) use ($self) {
                    $self->photoSrc = $file->src;
                    $self->save();
                    $file->tag = 'photo';
                    $file->save();
                });
            }
        }

        return $this;
    }








}
