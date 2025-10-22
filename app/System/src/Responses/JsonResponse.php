<?php

namespace App\System\Responses;

use App\System\Resources\JsonResource;
use Illuminate\Http\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    
    protected $dataBody = [];
    
    protected $message = '';
    
    protected $extensions = [];
    
    /**
     * JsonResponse constructor.
     * @param  mixed  $data  If array has key 'data' it spreads out.
     * @param  string  $message
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     */
    public function __construct($data = [], $message = '', $status = 200, $headers = [], $options = 0)
    {
        if (gettype($data) === 'array' && isset($data['data'])) {
            foreach ($data as $key => $item) {
                if ($key === 'data') {
                    $this->dataBody = $item;
                } else if ($key === 'message') {
                    $this->message = $message;
                } else {
                    $this->extensions[$key] = $item;
                }
            }
        } else {
            $this->dataBody = $data;
            $this->message = $message;
        }
        
        parent::__construct($this->constructData(), $status, $headers, $options);
    }
    
    /**
     * @return array
     */
    protected function constructData()
    {
        $result = array_merge([ 'message' => $this->message, ], $this->extensions);
        
        $data = $this->dataBody;
        if (gettype($data) === 'object' && is_a($data, JsonResource::class) && $data->isCollection()) {
            $data->setData($result);
            $result = $data;
        } else {
            $result['data'] = $data;
        }
        
        return $result;
    }
    
    /**
     * @param  string  $name
     * @param  mixed  $extension
     * @return JsonResponse
     */
    public function add($name, $extension)
    {
        $this->extensions[$name] = $extension;
        
        $this->setData($this->constructData());
        
        return $this;
    }
    
    /**
     * @param  array|mixed  $extensions
     * @return JsonResponse
     */
    public function addMany($extensions)
    {
        foreach ($extensions as $name => $extension) {
            $this->extensions[$name] = $extensions;
        }

        $this->setData($this->constructData());
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function clearExtension()
    {
        $this->extensions = [];
    
        $this->setData($this->constructData());
    
        return $this;
    }
}
