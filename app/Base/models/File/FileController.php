<?php

namespace App\Base\Controllers\Api;

use App\App\Models\Passport;
use App\Base\Models\File;
use App\Base\Requests\CreateFileRequest;
use App\Base\Requests\EditFileRequest;
use App\Base\Resources\FileResource;
use App\System\Controllers\Controller;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @title Получить файл по id
     * @alias files.get
     * @path files/{file}
     */
    public function get(File $model)
    {
        $this->authorize('get', [ $model, ]);

        return $this->response(new FileResource($model));
    }

    /**
     * @title Сохранить файл
     * @alias files.post
     * @path files
     */
    public function post(CreateFileRequest $request)
    {
        $this->authorize('create', [ File::class, ]);

        $request->validate();

        $model = File::create(request()->all());

        return $this->response(new FileResource($model));
    }

    /**
     * @title Сохранить файл
     * @alias files.patch
     * @path files
     */
    public function patch(EditFileRequest $request)
    {
        $this->authorize('edit', [ File::class, ]);

        $request->validate();

        $data = request()->except('ownerId', 'ownerType');
        $data['owner'] = config('modules.base.file.ownerTypes')[request('ownerType')]::find('ownerId');
        $model = File::edit(request()->all());

        return $this->response(new FileResource($model));
    }

    /**
     * @title Удалить файл
     * @alias files.delete
     * @path files/{file}
     */
    public function delete(File $model)
    {
        $this->authorize('delete', [ $model, ]);

        $model->delete();

        return $this->response([], 'Файл удален');
    }

    /**
     * @title Подтвердить файл
     * @alias files.patchConfirm
     * @path files/{file}/confirm
     */
    public function patchConfirm(File $model)
    {
        $this->authorize('edit', [ File::class, ]);

        $model->isVerified = true;
        $model->save();

        return $this->response([], 'Файл подтвержден');
    }
}
