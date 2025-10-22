<?php

namespace App\Base\Controllers\Api;

use App\Base\Locale\LocaleService;
use App\Base\Models\Translation;
use App\Base\Requests\EditTranslationRequest;
use App\Base\Resources\TranslationResource;
use App\System\Controllers\Controller;
use App\System\Exceptions\NotFoundException;
use App\System\Requests\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * @title
     * @alias api.base.translations.get
     * @path base/translations/{tagName}
     */
    public function getByTag(Request $request, $tagName)
    {
        $this->authorize('read', [ Translation::class, ]);
        
        if (!in_array($tagName, Translation::tags)) {
            throw new NotFoundException('Translations with tag '.$tagName.' not exist');
        }
    
        $tagId = array_search($tagName, Translation::tags);
        
        $translations = Translation::query()
            ->select('*', DB::raw("length(field)-length(REPLACE(field,'.', '')) as lvl"))
            ->where('localeId', LocaleService::current()->id)
            ->where('tagId', $tagId)
            ->orderBy('lvl', 'desc')
            ->orderBy('id', 'desc')
            ->get()->keyBy('field');
        
        $result = [];
    
        foreach ($translations as $key => $translation) {
            $translations[$key] = (new TranslationResource($translation))->toArray($translation);
        }
        $translations = $translations->toArray();
        foreach ($translations as $translation) {
            $parts = explode('.', $translation['field']);
            array_pop($parts);
            $parentCode = implode('.', $parts);
            if ($parent = Arr::get($translations, $parentCode)) {
                addToChildren($parent, $translations[$translation['field']]);
                $translations[$parentCode] = $parent;
            } else {
                array_unshift($result, $translations[$translation['field']]);
            }
        }
        
        return $this->response($result);
    }
    
    /**
     * @title Изменить баннер
     * @alias api.base.translations.patch
     * @path base/translations/{translation}
     */
    public function patch(EditTranslationRequest $request, Translation $model)
    {
        $this->authorize('edit', $model);
        
        $request->validate();
        
        $model->edit(request()->all());
        
        return $this->response(new TranslationResource($model), 'Сохранено');
    }
}
