<?php

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\MorphPivot;


if (!function_exists('modules')) {
    /**
     * @return array
     * [
     *   'System' => [
     *     'models' => [
     *       [
     *         'name' => 'User', // filename
     *         'path' => 'D:...app/System/models/User/User.php',
     *         'class' => 'App\System\Models\User',
     *       ],
     *     ],
     *     'controllers' => [],
     *     'resources' => [],
     *     ...
     *   ],
     * ]
     */
    function modules()
    {
        $types = [
            'controllers', 'factories', 'policies', 'requests', 'resources', 'seeders', 'models', 'migrations',
        ];
        $files = [];
        
        foreach (config('modules.imports', []) as $moduleName) {
            $module = [];
            // add files from root
            foreach (glob(app_path("$moduleName/models/*/*.php")) as $item) {
                if (strpos($item, 'Controller.php')) {
                    $module['controllers'][] = [ 'path' => $item, ];
                } else {
                    if (strpos($item, 'Factory.php')) {
                        $module['factories'][] = [ 'path' => $item, ];
                    } else {
                        if (strpos($item, 'Policy.php')) {
                            $module['policies'][] = [ 'path' => $item, ];
                        } else {
                            if (strpos($item, 'Request.php')) {
                                $module['requests'][] = [ 'path' => $item, ];
                            } else {
                                if (strpos($item, 'Resource.php')) {
                                    $module['resources'][] = [ 'path' => $item, ];
                                } else {
                                    if (strpos($item, 'Seeder.php')) {
                                        $module['seeders'][] = [ 'path' => $item, ];
                                    } else {
                                        if (strpos($item, '_')) {
                                            $module['migrations'][] = [ 'path' => $item, ];
                                        } else {
                                            $module['models'][] = [ 'path' => $item, ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // add files from folders
            foreach ($types as $type) {
                $folderPaths = glob(app_path("$moduleName/models/*/".Str::ucfirst($type)."/*.php"));
                $folderPaths = array_map(function ($item) {
                    return [ 'path' => $item, ];
                }, $folderPaths);
                $module[$type] = array_merge(Arr::get($module, $type, []), $folderPaths);
            }
            
            //
            //
            
            // models
            $module['models'] = array_map(function ($item) use ($moduleName) {
                $segments = explode('/', $item['path']);
                $item['name'] = explode('.php', $segments[count($segments) - 1])[0];
                $item['class'] = "App\\$moduleName\Models\\".$item['name'];
                return $item;
            }, $module['models']);
            
            // policies
            $module['policies'] = array_map(function ($item) use ($moduleName) {
                $segments = explode('/', $item['path']);
                $item['name'] = explode('.php', $segments[count($segments) - 1])[0];
                $modelName = explode('Policy', $item['name'])[0];
                $item['class'] = "App\\$moduleName\Policies\\".$item['name'];
                $item['model'] = "App\\$moduleName\Models\\".$modelName;
                return $item;
            }, $module['policies']);
            
            // seeders
            $module['seeders'] = array_map(function ($item) use ($moduleName) {
                $segments = explode('/', $item['path']);
                $item['name'] = explode('.php', $segments[count($segments) - 1])[0];
                $item['class'] = "App\\$moduleName\Seeders\\".$item['name'];
                return $item;
            }, $module['seeders']);
            
            $files[$moduleName] = $module;
        }
        
        return $files;
    }
}

if (!function_exists('table')) {
    /**
     * @param  string  $class
     * @param  string  $column
     * @return string
     */
    function table($class, $column = null)
    {
        $table = (new $class)->getTable();
        $column = $column ? '.'.$column : '';
        return $table.$column;
    }
}

if (!function_exists('is_missed')) {
    /**
     * @param  string  $class
     * @param  string  $column
     * @return string
     */
    function is_missed($value)
    {
        return is_a($value, \Illuminate\Http\Resources\MissingValue::class);
    }
}

if (!function_exists('stage')) {
    /**
     * @param  null  $value
     * @return string|bool
     */
    function stage($value = null)
    {
        if ($value === null) {
            return config('app.stage');
        } else {
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (config('app.stage') === $v) {
                        return true;
                    }
                }
                return false;
            } else {
                return config('app.stage') === $value;
            }
        }
    }
}

if (!function_exists('creator')) {
    class Creator
    {
        protected $class;
        protected $data;
        protected $dataClass;
        protected $state;
        protected $func = 'create';
        
        /**
         * @param  Model|Pivot|MorphPivot|string  $class
         * @param  mixed  $data
         */
        public function __construct($class, $data = [], $dataClass = null)
        {
            $this->class = $class;
            $this->data = $data;
            $this->dataClass = $dataClass;
        }
        
        public function create()
        {
            $factory = factory($this->class);
            if ($this->state) {
                $factory->state($this->state);
            }
            $data = array_merge($factory->raw(), $this->data);
            
            return $this->class::{$this->func}($this->dataClass ? new $this->dataClass($data) : $data);
        }
    }
    
    /**
     * @param  Model|Pivot|MorphPivot|string  $class
     * @param  mixed  $data
     * @return Creator
     */
    function creator($class, $data = [], $dataClass = null)
    {
        return new Creator($class, $data, $dataClass);
    }
}

if (!function_exists('transliterate')) {
    /**
     * @param  string  $string
     * @param  bool  $lowercase
     * @param  string  $delimiter
     * @return string
     */
    function transliterate($string, $lowercase = true, $delimiter = '_')
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];
        $string = strtr($string, $converter);
        if ($lowercase) {
            $string = strtolower($string);
        }
        if ($delimiter) {
            $string = preg_replace('~[^-a-z0-9_]+~u', $delimiter, $string);
            $string = trim($string, $delimiter);
        }
        
        return $string;
    }
}

if (!function_exists('addToChildren')) {
    function addToChildren(&$container, $item, $name = 'children', $prepend = true)
    {
        if (!Arr::get($container, $name)) {
            $container[$name] = [];
        }
        
        if ($prepend) {
            array_unshift($container[$name], $item);
        } else {
            $container[$name][] = $item;
        }
    }
}

if (!function_exists('getUrl')) {
    function getUrl($name, $params = [], $locale = 'ru')
    {
        if ($locale !== 'ru') {
            $params['lang'] = $locale;
        } elseif (\App\Base\Locale\LocaleService::current()->name !== 'ru') {
            $params['lang'] = \App\Base\Locale\LocaleService::current()->name;
        }
        return route($name, $params);
    }
}
