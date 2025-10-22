<?php

namespace App\System\Controllers;

use App\System\Responses\JsonResponse;
use App\Laravel\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @param  array  $data
     * @param  string  $message
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return JsonResponse
     */
    protected function response($data = [], $message = '', $status = 200, $headers = [], $options = 0) {
        return (new JsonResponse($data, $message, $status, $headers, $options));
    }
}
