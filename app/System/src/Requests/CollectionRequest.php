<?php

namespace App\System\Requests;

class CollectionRequest extends Request
{
    
    /**
     * @page  {integer}
     * @size  {integer}
     * @all  {boolean}
     * @tree  {boolean}
     *
     * @sort  {array}
     * @search  {array}
     * @filter  {string}  Don't works in swagger
     * @byKey  {string}
     *
     * @fields  {string}
     * @with  {string}  Don't works in swagger
     */
    public function rules()
    {
        return [
            'page' => [ 'string', ],
            'size' => [ 'string', ],
            'all' => [ 'boolean', ],
            'sort' => [ 'array', ],
            'search' => [ 'string|array', ],
            'filter' => [ 'array', ],
            'fields' => [ 'string', ],
            'with' => [ 'array' ],
        ];
    }
    
//    public function info()
//    {
//        return [
//            'page' => 1,
//            'size' => 10,
//            'count' => 33,
//            "sort" => [
//                0 => "id,desc",
//            ],
//            "search" => [
//                0 => "quizzz AND asdasd",
//                1 => "eqwe",
//            ],
//            "searchBy" => [
//                0 => "title",
//                1 => "admin",
//            ],
//            "fields" => [
//                0 => "title",
//                1 => "desc",
//            ],
//            "filters" => [ // search{string}, is{all}, between{number, timestamp}
//                "user_id" => "19",
//                "user_id" => "not:19,20,21",
//                "type" => "in:one,two,four",
//                "title" => "search:qwe",
//                "date" => "between:null,19323247932",
//            ],
//            "with" => [
//                "variants" => [
//                    0 => "id",
//                    1 => "title",
//                ],
//                "questions" => null,
//            ],
//        ];
//    }
}
