<?php

namespace App\System\Rules;

use App\System\Requests\Request;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class RelationRule implements Rule
{
    /** @var Request */
    protected $request;
    
    protected $message = 'The validation error message.';
    
    
    /**
     * Create a new rule instance.
     *
     * @param string $request
     */
    public function __construct($request)
    {
        $this->request = new $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value) || !Arr::isAssoc($value)) {
            $this->message = 'Must be an object';
            return false;
        }
        
        if (isset($value['id'])) {
            $this->request->uniqueExcept($value['id']);
        }
        
        $validator = Validator::make($value, $this->request->rules());
        if ($validator->fails()) {
            $this->message = [ $validator->errors()->messages() ];
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
