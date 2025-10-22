<?php

namespace App\Base\Models;

use App\Auth\Models\User;
use App\Base\Services\FileService;
use App\System\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile as UploadedFile;

/**
 * Этот класс описывает конкретный физический файл из хранилища
 */
class File extends Model
{
    use SoftDeletes;

    protected $table = 'base__files';

    protected $attributes = [
        'ownerId' => null,
        'ownerType' => null,
        'tag' => null,
        'description' => '',
        'size' => null,
        'isExternal' => false,
        'isVerified' => false,
        'position' => 0,
    ];

    protected $casts = [
        'id' => 'integer',
        'authorId' => 'integer', // кто создал файл в системе
        'ownerId' => 'integer', // к какой модели файл относится
        'ownerType' => 'string',
        'tag' => 'string', // по желанию, можно делить файлы на категории
        'name' => 'string',
        'description' => 'string',
        'size' => 'integer', // размер файла в байтах
        'src' => 'string', // путь к файлу
        'isExternal' => 'boolean',
        'isVerified' => 'boolean', //Подтвержден ли файл
        'position' => 'integer', // для сортировки
        'createdAt' => 'datetime',
        'deletedAt' => 'datetime',
    ];

    public const max = [
        'tag' => 127,
        'name' => 511,
        'description' => 2047,
        'src' => 255,
    ];

    public const from = [
        'file', 'url', 'base64', 'local', 'mock',
    ];


    /*************
     * Relations *
     *************/

    public function author()
    {
        return $this->belongsTo(User::class, 'authorId');
    }

    public function owner()
    {
        return $this->morphTo(null, 'ownerType', 'ownerId');
    }


    /**************
     * Attributes *
     **************/

    public function getUrlAttribute()
    {
        return FileService::publicPath($this->src);
    }


    /**************
     * Operations *
     **************/

    /**
     * <code>
     * $data = [
     *   'file' => UploadedFile or string, // string if from url or base64
     *   'from' => string, // default 'file' // self::from
     *   'position' => integer, // default 0
     *   'tag' => string, // default null
     *   'name' => string, // default file name
     *   'description' => string, // default ''
     *   'owner' => Model, // default null
     *   'authorId' => User::id, // default auth()->user()->id
     * ];
     * </code>
     *
     * @param  array  $data
     * @return File
     */
    public static function create($data = [])
    {
        $model = new self();
        $model->postFields([ 'tag', 'name', 'description', 'position', ], $data);

        if ($owner = Arr::get($data, 'owner')) {
            $model->owner()->associate($owner);
        }
        if ($authorId = Arr::get($data, 'authorId')) {
            $model->authorId = $authorId;
        } else {
            $model->authorId = auth()->user()->id;
        }

        switch (Arr::get($data, 'from', 'file')) {
            case 'url':
                self::saveUrl($data['file'], $model);
                break;
            case 'base64':
                self::saveBase64($data['file'], $model);
                break;
            case 'local':
                self::saveLocal($model);
                break;
            case 'mock':
                self::saveMock($model);
                break;
            default:
                self::saveFile($data['file'], $model);
        }

        return $model;
    }

    /**
     * <code>
     * $data = [
     *   'tag' => string,
     *   'name' => string,
     *   'position' => integer,
     *   'owner' => Model,
     * ];
     * </code>
     *
     * @param  array  $data
     */
    public function edit($data)
    {
        $this->postFields([ 'tag', 'name', 'description', 'position', 'isVerified' ], $data);
        if ($owner = Arr::get($data, 'owner')) {
            $this->owner()->associate($owner);
        }
        $this->save();
    }

    /**
     * Delete the model from the database.
     * @return bool|null
     */
    public function forceDelete()
    {
        if (!$this->isExternal) {
            FileService::removeFileOrDir($this->src);
        }
        return parent::forceDelete();
    }


    /*********************
     * Private functions *
     *********************/

    private static function saveFile(UploadedFile $file, File $model)
    {
        $ext = $file->guessExtension() ?: $file->getClientOriginalExtension();
        if (in_array($ext, [ 'php', 'php3', 'php4', 'php5', 'phps' ])) {
            $ext = 'txt';
        }
        if ($model->name) {
            $fileName = transliterate($model->name);
            if (count(explode('.', $model->name)) < 2) {
                $model->name .= '.'.$ext;
                $fileName .= '.'.$ext;
            }
        } else {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = transliterate($name).'.'.$ext;
            $model->name = $name.'.'.$ext;
        }

        $model->size = $file->getSize();
        $model->save();
        $model->src = FileService::srcPath($model, $fileName);
        $model->save();

        $file->storeAs('', FileService::storagePath($model->src));
    }

    private static function saveUrl($url, File $model)
    {
        if ($model->name) {
            if (count(explode('.', $model->name)) < 2) {
                $parts = explode('.', $url);
                $ext = $parts[count($parts) - 1];
                $model->name .= '.'.$ext;
            }
        } else {
            $parts = explode('/', $url);
            $model->name = $parts[count($parts) - 1];
        }
        $model->src = $url;
        $model->isExternal = true;
        $model->save();
    }

    private static function saveMock(File $model)
    {
        $file = UploadedFile::fake()->image('fake.jpg', 1000, 1000)->size(100);
        self::saveFile($file, $model);
    }

    private static function saveLocal(File $model, $path)
    {
        $file = UploadedFile::fake()->image('fake.jpg', 1000, 1000)->size(100);
        self::saveFile($file, $model);
    }

    private static function saveBase64($file, $model)
    {
        // TODO do base64 saving
        return self::saveMock($model);
    }
}
