<?php

namespace App\System\Requests;

/**
 * @name GetOneRequest
 * @description Описание реквеста
 */
class GetOneRequest extends Request
{
    
    /**
     * @fields  {string}
     * @with  {string}  Don't works in swagger
     */
    public function rules()
    {
        return [
            'fields' => [ 'array', ],
            'with' => [ 'array' ],
        ];
    }
}
