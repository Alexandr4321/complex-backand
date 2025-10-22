<?php

namespace App\Base\Controllers\Web;

use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Controllers\Controller;
use App\System\Exceptions\PageNotFoundException;
use App\System\Requests\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileControllerWeb extends Controller
{
    /**
     * @title Получить содержимое файла
     * @alias file.get
     * @version false
     * @path files/{id}/{name}
     */
    public function get(Request $request, $id, $name)
    {
        if (!($file = File::query()->find($id)) || $file->src !== FileService::srcPath($file, $name)) {
            throw new PageNotFoundException();
        }
        
        $path = FileService::storagePath($file->src);
        
        /** TODO переделать deprecated */
        return Response::create(Storage::get($path), 200, [
            'Content-Type' => Storage::mimeType($path),
        ]);
    }
}
