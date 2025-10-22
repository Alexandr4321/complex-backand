<?php

namespace App\Auth\Controllers\Api;

use App\Auth\Models\Access;
use App\Auth\Resources\AccessResource;
use App\Auth\Types\TypeAccessCreate;
use App\Auth\Requests\CreateAccessRequest;
use App\Auth\Helpers\UserService;
use App\System\Controllers\Controller;

/**
 * @group Auth / Access
 */
class AccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * @title Назначить полномочие
     * @alias auth.accesses.post
     * @path auth/accesses
     */
    public function post(CreateAccessRequest $request)
    {
//        $this->authorize('create', [ Access::class, ]);
        
        $request->validate();
        
        $accessClass = Access::types[request('type')];
        $ownerClass = Access::ownerTypes[request('ownerType')];
        
        $model = Access::create(new TypeAccessCreate([
            'access' => $accessClass::where('name', request('name'))->first(),
            'owner' => $ownerClass::find(request('ownerId')),
            'contractor' => UserService::currentCompany(),
            'modelId' => request('modelId'),
            'data' => request('data'),
        ]));
        
        return $this->response(new AccessResource($model), 'Полномочие назначено');
    }
    
    /**
     * @title Отозвать полномочие
     * @alias auth.accesses.delete
     * @path auth/accesses/{access}
     */
    public function delete(Access $model)
    {
//        $this->authorize('delete', $model);
        
        $model->delete();
        
        return $this->response([], 'Полномочие отозвано');
    }
}
