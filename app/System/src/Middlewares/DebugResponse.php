<?php

namespace App\System\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class DebugResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if (app()->bound('debugbar') && app('debugbar')->isEnabled() && $request->expectsJson()) {
            $queries_data = $this->sqlFilter(app('debugbar')->getData());
            $content = json_decode($response->getContent(), true) ?: [];
            $debug = [
                'debug' => [
                    'total' => count($queries_data),
                    'queries' => $queries_data,
                ]
            ];
            $response->setContent(json_encode(array_merge($content, $debug)));
        }
        
        return $response;
    }
    
    /**
     * Get only sql and each duration
     *
     * @param $debugbar_data
     * @return array
     */
    protected function sqlFilter($debugbar_data) {
        $result = Arr::get($debugbar_data, 'queries.statements', []);
        
        return array_map(function ($item) {
            return [
                'sql' => Arr::get($item, 'sql'),
                'duration' => Arr::get($item, 'duration_str'),
            ];
        }, $result);
    }
}
