<?php

namespace App\System\Requests;

use App\System\Models\Model;
use Illuminate\Http\Request as BaseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Unique;

/**
 * @description Request
 */
class Request extends BaseRequest
{

    protected $uniqueExceptId = '';

    /**
     *
     */
    public function rules()
    {
        return [];
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param $callback
     * @return void
     *
     * @throws ValidationException
     */
    public function validate($callback = '')
    {
        $validator = Validator::make(request()->all(), $this->rules());

        if ($validator->fails()) {
            if (is_callable($callback)) {
                $callback($validator);
            }
            throw new ValidationException($validator);
        }
    }

    public function uniqueExcept($id)
    {
        $this->uniqueExceptId = $id;
    }

    /**
     * @param  Model|string  $value
     * @param  string  $column
     * @return Unique
     */
    protected function ruleUnique($value, $column = 'NULL')
    {
        $table = $value;
        if (is_a($value, 'Illuminate\Database\Eloquent\Model')) {
            $table = (new $value())->getTable();
        }
        return Rule::unique($table, $column)->ignore($this->uniqueExceptId);
    }
    
    /**
     * @param  Model|string  $value
     * @param  string  $column
     * @return Exists
     */
    protected function ruleExists($tableOrClass, $column = 'id')
    {
        $table = $tableOrClass;
        if (is_a($tableOrClass, 'Illuminate\Database\Eloquent\Model')) {
            $table = (new $tableOrClass())->getTable();
        }
        return Rule::exists($table, $column);
    }
}
