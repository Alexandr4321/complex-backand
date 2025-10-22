<?php

namespace App\Auth\Models;

use App\App\Models\Avatar;
use App\App\Models\Branch;
use App\App\Models\Department;
use App\App\Models\Passport;
use App\App\Models\Positions;
use App\Auth\Helpers\UserService;
use App\Auth\Service\AccessService;
use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Models\PatchModel;
use App\Laravel\User as LaravelUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends LaravelUser
{
    use PatchModel, HasApiTokens, SoftDeletes;

    const CREATED_AT = 'createdAt';
    const DELETED_AT = 'deletedAt';
    const UPDATED_AT = null;

    protected $table = 'auth__users';

    protected $casts = [
        'id' => 'integer',

        'firstName' => 'string',
        'lastName' => 'string',
        'surName' => 'string',

        'complexId' => 'integer',
        'homeId' => 'integer',
        'entranceId' => 'integer',
        'floorId' => 'integer',
        'apartmentId' => 'integer',

        'login' => 'string',
        'password' => 'string',
        'phone' => 'string',

        'isAdmin' => 'boolean',
        'isRegistered' => 'boolean',
        'createdAt' => 'datetime',

    ];

    protected static function attributes()
    {
        return [
            'phoneVerifyToken' => UserService::createPhoneVerifyToken(),

        ];
    }


    /*************
     * Relations *
     *************/


    /**************
     * Operations *
     **************/


    /**
     * @param array $data
     * @return User
     */
    public static function create($data)
    {
        $model = new User();
        $model->firstName = Arr::get($data, 'firstName');
        $model->lastName = Arr::get($data, 'lastName');
        $model->surName = Arr::get($data, 'surName');

        $model->complexId = Arr::get($data, 'complexId');
        $model->homeId = Arr::get($data, 'homeId');
        $model->entranceId = Arr::get($data, 'entranceId');
        $model->floorId = Arr::get($data, 'floorId');
        $model->apartmentId = Arr::get($data, 'apartmentId');

        $model->phone = UserService::clearPhone(Arr::get($data, 'phone'));
        $model->login = Arr::get($data, 'login');
        $model->password = UserService::cryptPassword($data['password']);
        $model->isAdmin = Arr::get($data, 'isAdmin', false);
        $model->save();
        return $model;
    }

    /**
     * @param $data
     */
    public function edit($data)
    {
        $this->postFields(['surName','complexId','homeId','entranceId','floorId','apartmentId','isAdmin', 'password', 'lastName', 'firstName', 'phone', 'login' ], $data);

        $this->save();
    }
    /**
     * @param string $password
     */
    public function editPassword($password)
    {
        $this->password = UserService::cryptPassword($password);
        $this->save();
    }
}
