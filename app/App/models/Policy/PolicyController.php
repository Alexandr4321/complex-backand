<?php

namespace App\App\Controllers\Api;

use App\App\Models\Policy;
use App\App\Requests\EditPolicyRequest;
use App\App\Resources\PolicyResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;

/**
 * @group Privacy Policy
 */
class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Получить политику конфиденциальности
     * @alias api.privacypolicy.get
     * @path privacypolicy
     */
    public function get(Request $request)
    {

        $model = Policy::query()->first();

        return $this->response(new PolicyResource($model));
    }

    /**
     * @title Редактировать политику конфиденциальности
     * @alias api.privacypolicy.patch
     * @path privacypolicy/{policy}
     */
    public function patch(EditPolicyRequest $request, Policy $policy)
    {
        // Валидация входных данных
        $request->validate();
        $data = request()->all();

        // Обновление политики конфиденциальности
        $policy->edit($data);

        return $this->response(new PolicyResource($policy), 'Политика конфиденциальности успешно обновлена');
    }
}
