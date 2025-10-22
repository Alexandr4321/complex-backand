<?php

namespace App\App\Models;

use App\Base\Locale\Localized;
use App\System\Models\Model;

class Policy extends Model
{
    use Localized;

    protected $table = 'app__policy';

    protected $casts = [
        'id' => 'integer',
        'createdAt' => 'datetime',
    ];

    public const localized = [
        'content',
    ];

    protected $with = ['translations'];

    /*************
     * Relations *
     *************/


    /**************
     * Operations *
     **************/

    /**
     * @param  array  $data
     * @return Policy
     */
    public static function create($data)
    {
        $model = new self();

        $model->content = $data['content'];

        $model->save();


        return $model;
    }

    /**
     * @param $data
     * @return $this|Policy
     */
    public function edit($data)
    {
        $this->postFields(['content', ], $data);
        $this->save();

        return $this;
    }
}
