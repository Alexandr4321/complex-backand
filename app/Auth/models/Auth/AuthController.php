<?php

namespace App\Auth\Controllers\Api;

use App\App\Models\Department;
use App\App\Models\Sms;
use App\Auth\Helpers\AppService;
use App\Auth\Helpers\UserService;
use App\Auth\Models\User;
use App\Auth\Requests\LoginRequest;
use App\Auth\Requests\RegisterRequest;
use App\Auth\Requests\ResetPasswordRequest;
use App\Auth\Resources\AuthResource;
use App\Auth\Resources\UserResource;
use App\System\Controllers\Controller;
use App\System\Exceptions\BusinessException;
use App\System\Exceptions\ValidationException;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(Route $route)
    {
        $action = $route->getActionMethod();

        if (in_array($action, ['get'])) {
            $this->middleware('auth:api');
        }
    }

    /**
     * @title Получить информацию о залогиненом пользователе
     * @alias auth.get
     * @path auth/info
     */
    public function get()
    {
        return $this->response((new AuthResource(auth()->user())));
    }

    /**
     * @title Получить информацию о залогиненом пользователе
     * @alias auth.postSendCode
     * @path auth/send-code/{phone}
     */
    public function postSendCode($phone)
    {
        $code = UserService::createPhoneVerifyToken();

        $type = Arr::get(request(), 'type', 'recover');
        if ($type === 'registration') {
            $user = User::query()->where('phone', UserService::clearPhone($phone))->first();
            if ($user) {
                throw new BusinessException('Пользователь с таким номером уже зарегистрирован');
            }
        }
        $content = 'Код подтверждения в приложении : ' . $code;
        if (config('app.stage') !== 'production') {
            Sms::create([
                'phone' => UserService::clearPhone($phone),
                'code' => '0000',
            ]);
        } else {
            AppService::sendSms($phone, $content);

            Sms::create([
                'phone' => UserService::clearPhone($phone),
                'code' => $code,
            ]);
        }

        return $this->response([]);
    }

    /**
     * @title Проверить код
     * @alias auth.postCheckCode
     * @path auth/check-code/{phone}/{code}
     */
    public function postCheckCode($phone, $code)
    {
        $sendedModel = Sms::query()->where('phone', UserService::clearPhone($phone))
            ->where('code', $code)
            ->where('isVerified', false)
            ->orderBy('createdAt', 'desc')->first();

        if (!$sendedModel) {
            throw new BusinessException('Код введен неверно');
        }

        if ($sendedModel->expirationTime < now()) {
            throw new BusinessException('Время действия кода истекло');
        }

        $sendedModel->isVerified = true;
        $sendedModel->save();

        return $this->response(['Подтверждено']);
    }

    /**
     * @title Регистрация
     * @alias auth.postRegister
     * @path auth/register
     */
    public function postRegister(RegisterRequest $request)
    {
        $request->validate();

        $user = User::query()->where('login', UserService::clearPhone(request('login')))->first();

        if ($user && $user->isRegistered === true) {
            throw new BusinessException('Пользователь с таким номером телефона уже зарегистрирован');
        }
        if ($user && $user->isRegistered === false) {
            $user->isRegistered = true;
            $user->password = UserService::cryptPassword(request('password'));
            $user->save();
            $model = $user;
        } else {
            $model = User::create(request()->all());
        }
        return $this->response(new UserResource($model));
    }

    /**
     * @title Регистрация
     * @alias auth.postResetPassword
     * @path auth/reset-password
     */
    public function postResetPassword(ResetPasswordRequest $request)
    {
        $request->validate();
        $model = User::query()->where('login', UserService::clearPhone(request('login')))->first();
        if (!$model) {
            throw new BusinessException('Пользователь не найден');
        }

        $model->password = UserService::cryptPassword(request('password'));
        $model->save();

        return $this->response(new UserResource($model));
    }

    /**
     * @title Логин
     * @alias auth.postLogin
     * @path auth/login
     */
    public function postLogin(LoginRequest $request)
    {
        $request->validate();

        $user = User::query()->where('phone',  UserService::clearPhone(request('login')))->first();

        if ($user && !is_null($user->password) && Hash::check(request('password'), $user->password)) {
            $token = $user->createToken('MyApp')->accessToken;
            return $this->response(new AuthResource($user, ['token' => $token,]));
        }

        throw new ValidationException('Validation errors.', [
            'login' => ['Неправильный логин или пароль',],
        ]);
    }

    /**
     * @title Логин админки
     * @alias auth.postLoginAdmin
     * @path admin/auth/login
     */
    public function postLoginAdmin(LoginRequest $request)
    {
        $request->validate();

        $user = User::query()->where('fullName', request('login'))->first();

        if ($user && !is_null($user->password) && Hash::check(request('password'), $user->password)) {
            $token = $user->createToken('MyApp')->accessToken;
            return $this->response(new AuthResource($user, ['token' => $token,]));
        }

        throw new ValidationException('Validation errors.', [
            'login' => ['Неправильный телефон или пароль',],
        ]);
    }
}
