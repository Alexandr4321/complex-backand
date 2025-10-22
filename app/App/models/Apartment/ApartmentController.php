<?php

namespace App\App\Controllers\Api;

use App\App\Models\Apartment;
use App\App\Requests\CreateApartmentRequest;
use App\App\Resources\ApartmentResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group Apartment
 */
class ApartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть квартиру
     * @alias api.apartments.get
     * @path apartments/{apartment}
     */
    public function get(Request $request, Apartment $model)
    {
        $this->authorize('read', $model);

        return $this->response(new ApartmentResource($model));
    }



    /**
     * @title Получить список домов
     * @alias api.apartments.getAll
     * @path  apartments
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [Apartment::class,]);

        $result = Apartment::query();

        return $this->response(new ApartmentResource($result));
    }


    /**
     * @title Получить список квартир
     * @alias api.apartments.getMy
     * @path my/apartments
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [Apartment::class]);

        $user = auth()->user();

        $orders = Apartment::where('userId', $user->id);

        return $this->response(new ApartmentResource($orders));
    }




    /**
     * @title Добавить квартиру
     * @alias api.apartments.post
     * @path apartments
     */
    public function post(CreateApartmentRequest $request)
    {
        $this->authorize('create', [Apartment::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = Apartment::query()->find($data['id']);
            if (!$model) {
                $model = Apartment::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = Apartment::create(request()->all());
        }

        return $this->response(new ApartmentResource($model), 'Квартира добавлена');
    }


    /**
     * @title Редактировать квартиру
     * @alias api.apartments.patch
     * @path apartments/{apartment}
     */
    public function patch(Request $request, Apartment $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new ApartmentResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить квартиру
     * @alias api.apartments.delete
     * @path apartments/{apartment}
     */
    public function delete(Apartment $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Квартира удалена');
    }


}
