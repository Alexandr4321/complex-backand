<?php

namespace App\App\Controllers\Api;

use App\App\Models\Card;

use App\App\Models\Home;
use App\App\Models\News;
use App\App\Models\Support;
use App\App\Requests\CreateCardRequest;
use App\App\Requests\CreateHomeRequest;
use App\App\Requests\CreateSupportRequest;
use App\App\Resources\CardResource;
use App\App\Resources\HomeResource;
use App\App\Resources\NewsResource;
use App\App\Resources\SupportResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group Support
 */
class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть Заявку
     * @alias api.supports.get
     * @path supports/{support}
     */
    public function get(Request $request, Support $model)
    {
        $this->authorize('read', $model);

        return $this->response(new SupportResource($model));
    }



    /**
     * @title Получить список Заявок
     * @alias api.supports.getAll
     * @path  supports
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [Support::class,]);

        $result = Support::query();

        return $this->response(new SupportResource($result));
    }

    /**
     * @title Получить список Заявок
     * @alias api.supports.getProductsActive
     * @path  active/supports
     */
    public function getProductsActive(Request $request)
    {
        $this->authorize('read', [Support::class,]);

        $result = Support::query()
            ->where('status', 'active');
        return $this->response(new SupportResource($result));
    }

    /**
     * @title Получить список Заявок
     * @alias api.supports.getMy
     * @path my/supports
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [Support::class]);

        $user = auth()->user();

        $orders = Support::where('userId', $user->id);

        return $this->response(new SupportResource($orders));
    }




    /**
     * @title Добавить Заявку
     * @alias api.supports.post
     * @path supports
     */
    public function post(CreateSupportRequest $request)
    {
        $this->authorize('create', [Support::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = Support::query()->find($data['id']);
            if (!$model) {
                $model = Support::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = Support::create(request()->all());
        }

        return $this->response(new SupportResource($model), 'Заявка сохранена');
    }


    /**
     * @title Редактировать Заявку
     * @alias api.supports.patch
     * @path supports/{support}
     */
    public function patch(Request $request, Support $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new SupportResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить Заявку
     * @alias api.supports.delete
     * @path supports/{support}
     */
    public function delete(Support $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Заявка удалена');
    }


}
