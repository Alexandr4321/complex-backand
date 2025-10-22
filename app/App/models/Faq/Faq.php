<?php

namespace App\App\Models;

use App\Base\Locale\Localized;
use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Models\Model;
use Illuminate\Support\Arr;


class Faq extends Model
{
    use Localized;

    protected $table = 'app__faqs';

    protected $casts = [
        'id' => 'integer',
        'createdAt' => 'datetime',
    ];

    public const localized = [
        'title','content',
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
     * @return Faq
     */
    public static function create($data)
    {
        $model = new self();

        $model->title = $data['title'];
        $model->content = $data['content'];

        $model->save();


        return $model;
    }



    /**
     * Update the title and content of the FAQ.
     *
     * @param  array  $data
     * @return $this|Faq
     */
    public function edit($data)
    {
        $this->postFields(['title', 'content',], $data);
        $this->save();
        return $this;
    }





    /**
     * @return bool|null
     */
    public function delete()
    {

        return parent::delete();
    }
}
