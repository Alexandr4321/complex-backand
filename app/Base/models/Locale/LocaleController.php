<?php

namespace App\Base\Controllers\Api;

use App\Base\Models\Locale;
use App\Eq\Resources\ActiveLocaleResource;
use App\Eq\Resources\LocaleResource;
use App\System\Controllers\Controller;
use App\System\Requests\Request;

/**
 * @group Locale
 */
class LocaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * @title Получить все локали
     * @description Используется администраторами чтобы увидеть скрытые локали
     * @alias locale.getList
     * @path locales
     */
    public function getList(Request $request)
    {
        $this->authorize('read', Locale::class);
        
        return $this->response(new LocaleResource(Locale::class));
    }
    
    /**
     * @title Получить все активные локали
     * @description Используется для всех пользователей
     * @alias locale.getActiveList
     * @path locales/active
     */
    public function getActiveList(Request $request)
    {
        $this->authorize('readActive', Locale::class);
        
        return $this->response(new ActiveLocaleResource(Locale::query()->where('isHidden', false)));
    }
}
