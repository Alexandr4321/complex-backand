<?php

namespace App\Auth\Controllers\Api;

use App\App\Models\Tractor;
use App\App\Resources\TractorResource;
use App\Auth\Helpers\UserService;
use App\Auth\Models\Access;
use App\Auth\Models\Permit;
use App\Auth\Models\User;
use App\Auth\Requests\CreateUserRequest;
use App\Auth\Requests\EditPasswordRequest;
use App\Auth\Requests\EditUserRequest;
use App\Auth\Requests\EmailTokenRequest;
use App\Auth\Resources\UserResource;
use App\Auth\Types\TypeAccessCreate;
use App\System\Controllers\Controller;
use App\System\Exceptions\BusinessException;
use App\System\Exceptions\ValidationException;
use App\System\Requests\CollectionRequest;
use App\System\Requests\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Получить пользователя по id
     * @alias users.get
     * @path users/{user}
     */
    public function get(User $model)
    {
        $this->authorize('readOne', $model);

        return $this->response(new UserResource($model));
    }


    /**
     * @title Почта
     * @alias users.postSendMessage
     * @path users/{user}/send-message
     */
    public function postSendMessage(Request $request, User $user)
    {
        $confirmationCode = rand(100000, 999999); // Генерация 6-значного кода
        $user->confirmationCode = $confirmationCode;
        $user->save();

        Mail::raw("Your confirmation code is: $confirmationCode", function ($mail) use ($user) {
            $mail->to($user->email)
                ->subject('Email Confirmation Code');
        });

        return $this->response(['message' => 'Confirmation code sent.']);
    }


    /**
     * @title Сделать продавцом
     * @alias users.postNowSeller
     * @path users/{user}/now-seller
     */
    public function postNowSeller(EditUserRequest $request, User $model)
    {
        $this->authorize('edit', $model);

        $model->nowSeller($model);

        return $this->response(new UserResource($model), 'Информация сохранена');
    }

    /**
     * @title Подтверждение почты
     * @alias users.postConfirmedMail
     * @path users/{user}/confirmed-mail
     */
    public function postConfirmedMail(Request $request, User $user)
    {
        $inputCode = (request('confirmationCode'));

        if ($user->confirmationCode == $inputCode) {
            $user->mailConfirmation = true;
            $user->confirmationCode = null;
            $user->save();

            return $this->response(['message' => 'Email confirmed.']);
        } else {
            return $this->response(['message' => 'Invalid confirmation code.'], 400);
        }

    }



    /**
     * @title Получить список пользователей
     * @alias users.getList
     * @path users
     */
    public function getList(CollectionRequest $request)
    {
        $this->authorize('read', User::class);

        $result = User::query()->where('isAdmin', false);

        return $this->response(new UserResource($result));
    }

    /**
     * @title Получить список пользователей
     * @alias users.getAdmins
     * @path admins
     */
    public function getAdmins(CollectionRequest $request)
    {
        $this->authorize('read', User::class);

        $result = User::query()->where('isAdmin', true);

        return $this->response(new UserResource($result));
    }

    /**
     * @title Получить список пользователей
     * @alias users.patchPassword
     * @path users/{user}/edit-password
     */
    public function patchPassword(EditPasswordRequest $request, User $model)
    {
        $this->authorize('editPass', [$model,]);

        if (!hash::check(request('oldPass'), $model->password)) {
            throw new BusinessException('', [
                'Op' => 'Старый пароль введен неверно',
                'Np' => '',
                'Cp' => '',
            ]);
        }
        if (request('oldPass') === request('newPass')) {
            throw new BusinessException('', [
                'Op' => '',
                'Np' => 'Новый пароль не должен совпадать с старым',
                'Cp' => '',
            ]);
        }
        if (request('newPass') !== request('confirmPass')) {
            throw new BusinessException('', [
                'Op' => '',
                'Np' => 'Пароли должны совпадать',
                'Cp' => 'Пароли должны совпадать',
            ]);
        }

        $model->password = UserService::cryptPassword(request('newPass'));
        $model->save();

        return $this->response(new UserResource($model));
    }

    /**
     * @title Создать пользователя
     * @alias users.post
     * @path users
     */
    public function post(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);

        $request->validate();

        $model = User::create(request()->all());
        Access::create(new TypeAccessCreate([
            'access' => Permit::query()->where('name', 'moderator')->first(),
            'owner' => $model,
        ]));

        return $this->response(new UserResource($model), 'Пользователь сохранен');
    }

    /**
     * @title Удалить аккаунт
     * @alias users.postDeleteAcc
     * @path users/{user}/delete-acc
     */
    public function postDeleteAcc(Request $request, User $model)
    {
        $this->authorize('deleteAcc', [$model,]);

        $request->validate();

        $model->isRegistered = false;
        $model->password = null;
        $model->save();

        return $this->response([], 'Аккаунт удален');
    }

    /**
     * @title Редактировать пользователя
     * @alias users.patch
     * @path users/{user}
     */
    public function patch(EditUserRequest $request, User $model)
    {
        $this->authorize('edit', $model);

        $request->uniqueExcept($model->id);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new UserResource($model), 'Информация сохранена');
    }

    /**
     * @title Удалить пользователя
     * @alias users.delete
     * @path users/{user}
     */
    public function delete(User $model)
    {
        $this->authorize('delete', $model);

        $model->delete();

        return $this->response([], 'Пользователь удален');
    }
}
