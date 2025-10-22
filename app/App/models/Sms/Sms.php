<?php

namespace App\App\Models;

use App\Base\Locale\Localized;
use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Models\Model;
use Illuminate\Support\Arr;

class Sms extends Model
{
    protected $table = 'app__sms';

    protected $casts = [
        'id' => 'integer',
        'phone' => 'string',
        'code' => 'string',
        'isVerified' => 'boolean',
        'expirationTime' => 'datetime',
        'createdAt' => 'datetime',
    ];

    /*************
     * Relations *
     *************/


    /**************
     * Operations *
     **************/

    /**
     * @param  array  $data
     * @return Sms
     */
    public static function create($data)
    {
        $model = new self();

        $model->phone = $data['phone'];
        $model->code = $data['code'];
        $model->expirationTime = now()->addMinutes(5);
        $model->isVerified = false;

        $model->save();


        return $model;
    }
    /**
     * @return bool|null
     */
    public function delete()
    {

        return parent::delete();
    }
}
