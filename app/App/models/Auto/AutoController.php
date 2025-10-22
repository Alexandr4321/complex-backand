<?php

namespace App\App\Controllers\Api;

use App\App\Models\Auto;
use App\App\Requests\CreateAutoRequest;
use App\App\Resources\AutoResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group Auto
 */
class AutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть машину
     * @alias api.autos.get
     * @path autos/{auto}
     */
    public function get(Request $request, Auto $model)
    {
        $this->authorize('read', $model);

        return $this->response(new AutoResource($model));
    }



    /**
     * @title Получить список машин
     * @alias api.autos.getAll
     * @path  autos
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [Auto::class,]);

        $result = Auto::query();

        return $this->response(new AutoResource($result));
    }


    /**
     * @title Получить список машин
     * @alias api.autos.getMy
     * @path my/autos
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [Auto::class]);

        $user = auth()->user();

        $orders = Auto::where('userId', $user->id);

        return $this->response(new AutoResource($orders));
    }




    /**
     * @title Добавить машину
     * @alias api.autos.post
     * @path autos
     */
    public function post(CreateAutoRequest $request)
    {
        $this->authorize('create', [Auto::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = Auto::query()->find($data['id']);
            if (!$model) {
                $model = Auto::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = Auto::create(request()->all());
        }

        return $this->response(new AutoResource($model), 'Авто добавлено');
    }


    /**
     * @title Редактировать машину
     * @alias api.autos.patch
     * @path autos/{auto}
     */
    public function patch(Request $request, Auto $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new AutoResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить машину
     * @alias api.autos.delete
     * @path autos/{auto}
     */
    public function delete(Auto $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Авто удалено');
    }


}
