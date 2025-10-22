<?php

namespace App\System\Generator\Console;

trait UseConfig
{
    /**
     * Get models array from config file
     * [ 'name' => [ ...model, ], ]
     *
     * @return array
     */
    protected function getModels($module)
    {
        $filePath = base_path('app/' . $module . '/config.php');
        
        $models = [];
        $index = 1;
        
        if (file_exists($filePath)) {
            $models = include $filePath;
        }
    
        foreach ($models as $name => $model) {
            if (!isset($model['plural'])) {
                $model['plural'] = isset($model['pivot']) ? $name : $name . 's';
            }
            if (!isset($model['props'])) {
                $model['props'] = [];
            } else if (is_string($model['props'])) {
                $model['props'] = explode('|', $model['props']);
            }
            if (!isset($model['relations'])) {
                $model['relations'] = [];
            }
            
            $model['index'] = $index++;
            $models[$name] = $model;
        }
        
        return $models;
    }
}
