<?php

namespace App\System\Swagger\Middleware;

use App\System\Requests\Request;
use App\System\Swagger\Services\SwaggerService;
use Closure;

class SwaggerMiddleware
{
    /**
     * @var SwaggerService
     */
    protected $service;
    
    /**
     * @var  bool  Skip collect data for current test.
     */
    public static $skipped = false;
    
    /**
     * SwaggerMiddleware constructor.
     */
    public function __construct()
    {
        $this->service = app(SwaggerService::class);
    }
    
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return  mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ((config('app.env') == 'testing') && !self::$skipped) {
            $this->service->addData($request, $response);
        }

        self::$skipped = false;

        return $response;
    }
}
