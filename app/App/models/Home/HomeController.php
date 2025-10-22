<?php

namespace App\App\Controllers\Api;

use App\App\Models\Card;

use App\App\Models\Home;
use App\App\Requests\CreateCardRequest;
use App\App\Requests\CreateHomeRequest;
use App\App\Resources\CardResource;
use App\App\Resources\HomeResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group Home
 */
class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть дом
     * @alias api.homes.get
     * @path homes/{home}
     */
    public function get(Request $request, Home $model)
    {
        $this->authorize('read', $model);

        return $this->response(new HomeResource($model));
    }



    /**
     * @title Получить список домов
     * @alias api.homes.getAll
     * @path  homes
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [Home::class,]);

        $result = Home::query();

        return $this->response(new HomeResource($result));
    }

    /**
     * @title Получить список карточек
     * @alias api.homes.getProductsActive
     * @path  active/homes
     */
    public function getProductsActive(Request $request)
    {
        $this->authorize('read', [Home::class,]);

        $result = Home::query()
            ->where('status', 'active');
        return $this->response(new HomeResource($result));
    }

    /**
     * @title Получить список карточек
     * @alias api.homes.getMy
     * @path my/homes
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [Home::class]);

        $user = auth()->user();

        $orders = Home::where('userId', $user->id);

        return $this->response(new HomeResource($orders));
    }




    /**
     * @title Добавить дом
     * @alias api.homes.post
     * @path homes
     */
    public function post(CreateHomeRequest $request)
    {
        $this->authorize('create', [Home::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = Home::query()->find($data['id']);
            if (!$model) {
                $model = Home::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = Home::create(request()->all());
        }

        return $this->response(new HomeResource($model), 'Карточка сохранена');
    }


    /**
     * @title Редактировать дом
     * @alias api.homes.patch
     * @path homes/{home}
     */
    public function patch(Request $request, Home $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new HomeResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить дом
     * @alias api.homes.delete
     * @path homes/{home}
     */
    public function delete(Home $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Карточка удалена');
    }


}
