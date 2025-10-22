<?php

namespace App\App\Controllers\Api;

use App\App\Models\Card;
use App\App\Models\Complex;
use App\App\Requests\CreateComplexRequest;
use App\App\Resources\ComplexResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group Complex
 */
class ComplexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть один ЖК
     * @alias api.complex.get
     * @path complexes/{complex}
     */
    public function get(Request $request, Complex $model)
    {
        $this->authorize('read', $model);

        return $this->response(new ComplexResource($model));
    }



    /**
     * @title Получить список всех ЖК
     * @alias api.complex.getAll
     * @path  complexes
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [Complex::class,]);

        $result = Complex::query();

        return $this->response(new ComplexResource($result));
    }



    /**
     * @title Добавить ЖК
     * @alias api.complex.post
     * @path complexes
     */
    public function post(CreateComplexRequest $request)
    {
        $this->authorize('create', [Complex::class,]);

        $request->validate();

        $model = Complex::create(request()->all());

        return $this->response(new ComplexResource($model), 'ЖК добавлен');
    }


    /**
     * @title Редактировать ЖК
     * @alias api.complex.patch
     * @path complexes/{complex}
     */
    public function patch(Request $request, Complex $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new ComplexResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить ЖК
     * @alias api.complex.delete
     * @path complexes/{complex}
     */
    public function delete(Complex $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'ЖК удалён');
    }


}
