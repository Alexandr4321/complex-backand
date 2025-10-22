<?php

namespace App\App\Controllers\Api;

use App\App\Models\Chat;
use App\App\Models\Faq;
use App\App\Requests\CreateChatRequest;
use App\App\Requests\CreateQuizRequest;
use App\App\Resources\ChatResource;
use App\App\Resources\FaqResource;
use App\App\Resources\PolicyResource;
use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Controllers\Controller;
use App\System\Requests\Request;
use Illuminate\Database\Eloquent\Builder;
use App\App\Requests\UpdateFaqRequest;

/**
 * @group Faq
 */
class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Получить список Faq
     * @alias api.faqs.getAll
     * @path faqs
     */
    public function getAll(Request $request)
    {
        $this->authorize('read', [Faq::class,]);

        $result = Faq::query();

        return $this->response(new FaqResource($result));
    }

    /**
     * @title Создать вопрос с ответом
     * @alias api.faqs.post
     * @path faqs
     */
    public function post(CreateQuizRequest $request)
    {
        $this->authorize('edit', [Faq::class,]);

        $request->validate();

        $model = Faq::create(request()->all());

        return $this->response(new FaqResource($model), '');
    }
    /**
     * @title Удалить вопрос с ответом
     * @alias api.faqs.delete
     * @path faqs/{faq}
     */
    public function delete(Faq $model)
    {
        $this->authorize('delete', [$model]); // Проверяем разрешение на удаление

        $model->delete(); // Удаляем вопрос

        return $this->response([], 'Вопрос успешно удален');
    }
    /**
     * Update the title and content of the FAQ.
     * @alias api.faqs.patch
     * @path faqs/{faq}

     */
    public function patch(UpdateFaqRequest $request, Faq $model)
    {
        $request->validate();

        $model->edit(request()->all());

        return $this->response(new PolicyResource($model), 'Информация сохранена');
    }

//    /**
//     * @title Отправить сообщение
//     * @alias api.chats.post
//     * @path chats
//     */
//    public function post(CreateChatRequest $request)
//    {
//        $this->authorize('edit', [Chat::class,]);
//
//        $request->validate();
//
//        $model = Chat::create(request()->all());
//
//        return $this->response(new ChatResource($model), '');
//    }
}
