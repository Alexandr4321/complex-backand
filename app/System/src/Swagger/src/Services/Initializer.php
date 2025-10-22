<?php

namespace App\System\Swagger\Services;

use App\System\Swagger\OpenApi\OpenApi;
use App\System\Swagger\OpenApi\OpenApiInfo;
use Illuminate\Support\Arr;

class Initializer
{
    
    public static function initializeData(OpenApi $api)
    {
        $api->setInfo(self::getInfo());
    
        foreach (config('swagger.servers') as $server) {
            $api->addServer($server['url'], Arr::get($server, 'description'));
        }
        
        $api->components->setSecuritySchema('jwt', [
            'type' => 'http',
            'scheme' => 'bearer',
        ]);
    }
    
    /**
     * @return OpenApiInfo
     * @throws \Throwable
     */
    protected static function getInfo()
    {
        $config = config('swagger.info');
    
        $info = new OpenApiInfo();
        $info->setTitle($config['title']);
        $info->setVersion($config['version']);
        $info->setTermsOfService($config['termsOfService']);
        $info->setLicence(Arr::get($config, 'licence.name'), Arr::get($config, 'licence.url'));
        $info->setDescription($config['descriptionView'] ?
            view($config['descriptionView'])->render()
            : $config['description']);
        $info->setContact(
            Arr::get($config, 'contact.name'),
            Arr::get($config, 'contact.url'),
            Arr::get($config, 'contact.email')
        );
    
        return $info;
    }
}
