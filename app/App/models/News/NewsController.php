<?php

namespace App\App\Controllers\Api;

use App\App\Models\Card;

use App\App\Models\Home;
use App\App\Models\News;
use App\App\Requests\CreateCardRequest;
use App\App\Requests\CreateHomeRequest;
use App\App\Requests\CreateNewsRequest;
use App\App\Resources\CardResource;
use App\App\Resources\HomeResource;
use App\App\Resources\NewsResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Support\Arr;


/**
 * @group News
 */
class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Посмотреть Новость
     * @alias api.newses.get
     * @path newses/{news}
     */
    public function get(Request $request, News $model)
    {
        $this->authorize('read', $model);

        return $this->response(new NewsResource($model));
    }



    /**
     * @title Получить список Новостей
     * @alias api.newses.getAll
     * @path  newses
     */
    public function getAll(Request $request)
    {
        $this->authorize('readAll', [News::class,]);

        $result = News::query();

        return $this->response(new NewsResource($result));
    }

    /**
     * @title Получить список Новостей
     * @alias api.newses.getProductsActive
     * @path  active/newses
     */
    public function getProductsActive(Request $request)
    {
        $this->authorize('read', [News::class,]);

        $result = News::query()
            ->where('status', 'active');
        return $this->response(new NewsResource($result));
    }

    /**
     * @title Получить список Новостей
     * @alias api.newses.getMy
     * @path my/newses
     */
    public function getMy(Request $request)
    {
        $this->authorize('read', [News::class]);

        $user = auth()->user();

        $orders = News::where('userId', $user->id);

        return $this->response(new NewsResource($orders));
    }




    /**
     * @title Добавить Новость
     * @alias api.newses.post
     * @path newses
     */
    public function post(CreateNewsRequest $request)
    {
        $this->authorize('create', [News::class,]);

        $request->validate();
        $data = request()->all();
        if (Arr::get($data, 'id')) {
            $model = News::query()->find($data['id']);
            if (!$model) {
                $model = News::create(request()->all());
            } else {
                $model->edit($data);
            }
        } else {
            $model = News::create(request()->all());
        }

        return $this->response(new NewsResource($model), 'Новость сохранена');
    }


    /**
     * @title Редактировать Новость
     * @alias api.newses.patch
     * @path newses/{news}
     */
    public function patch(Request $request, News $model)
    {
        $this->authorize('edit', $model);

        $request->validate();

        $model->edit(request()->all());

        return $this->response(new NewsResource($model), 'Изменения сохранены');
    }

    /**
     * @title Удалить новость
     * @alias api.newses.delete
     * @path newses/{news}
     */
    public function delete(News $model)
    {
        $this->authorize('edit', $model);

        $model->delete();

        return $this->response([], 'Новость удалена');
    }


}
