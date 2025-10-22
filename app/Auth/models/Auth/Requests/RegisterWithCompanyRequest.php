<?php

namespace App\Auth\Requests;

use App\Auth\Models\User;
use App\Company\Models\Company;
use App\System\Requests\Request;

class RegisterWithCompanyRequest extends Request
{
    public function rules()
    {
        return [
            'bin' => [ 'required', 'digits:12', $this->ruleUnique(Company::class), ],
            'companyTitle' => [ 'required', 'string', ],
            'position' => [ 'required', 'string', ],
            'iin' => [ 'required', 'digits:12', $this->ruleUnique(User::class), ],
            'password' => [ 'required', 'string', 'min:6', 'max:255', ],
            'email' => [ 'required', 'email', 'max:255', ],
            'fullName' => [ 'required', 'string', ],
        ];
    }
}
