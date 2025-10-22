<?php

namespace App\App\Controllers\Api;

use App\App\Models\GuestAuto;
use App\App\Requests\CreateGuestAutoRequest;
use App\App\Resources\AutoResource;
use App\App\Resources\GuestAutoResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group GuestAuto
 */
class GuestAutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть машину
     * @alias api.guestautos.get
     * @path guestautos/{guestauto}
     */
    public function get(Request $request, GuestAuto $model)
    {
        $this->authorize('read', $model);

        return $this->response(new AutoResource($model));
    }



    /**
     * @title Получить список машин
     * @alias api.guestautos.getAll
     * @path  guestautos
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [GuestAuto::class,]);

        $result = GuestAuto::query();

        return $this->response(new GuestAutoResource($result));
    }


    /**
     * @title Получить список машин
     * @alias api.guestautos.getMy
     * @path my/guestautos
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [GuestAuto::class]);

        $user = auth()->user();

        $orders = GuestAuto::where('userId', $user->id);

        return $this->response(new GuestAutoResource($orders));
    }




    /**
     * @title Добавить машину
     * @alias api.guestautos.post
     * @path guestautos
     */
    public function post(CreateGuestAutoRequest $request)
    {
        $this->authorize('create', [GuestAuto::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = GuestAuto::query()->find($data['id']);
            if (!$model) {
                $model = GuestAuto::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = GuestAuto::create(request()->all());
        }

        return $this->response(new GuestAutoResource($model), 'Авто добавлено');
    }


    /**
     * @title Редактировать машину
     * @alias api.guestautos.patch
     * @path guestautos/{guestauto}
     */
    public function patch(Request $request, GuestAuto $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new GuestAutoResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить машину
     * @alias api.guestautos.delete
     * @path guestautos/{guestauto}
     */
    public function delete(GuestAuto $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Авто удалено');
    }


}
