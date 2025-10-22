<?php

namespace App\System\Swagger\Services\Traits;

use Illuminate\Support\Arr;

trait ParseResponseTrait
{
    
    protected function parseResponse()
    {
        $produce = $this->response->headers->get('Content-type');
        if (is_null($produce)) {
            $produce = 'text/plain';
        }
        
        $responses = $this->item['responses'];
        $code = $this->response->getStatusCode();

        if (!in_array($code, $responses)) {
            $this->saveExample(
                $this->response->getStatusCode(),
                $this->response->getContent(),
                $produce
            );
        }
    }
    
    protected function saveExample($code, $content, $produce)
    {
        $availableContentTypes = [
            'application',
            'text'
        ];
        $explodedContentType = explode('/', $produce);
        
        if (in_array($explodedContentType[0], $availableContentTypes)) {
            $this->item['responses'][$code] = $this->makeResponseExample($content, $produce);
        } else {
            $this->item['responses'][$code] = '*Unavailable for preview*';
        }
    }
    
    protected function makeResponseExample($content, $mimeType, $description = '')
    {
        $c = [];
        
        if ($mimeType === 'application/json') {
            $c['application/json'] = [
                'example' => json_decode($content, true),
                'schema' => [
                    'type' => 'object',
                    'properties' => $this->getProperties($this->response->original),
                ],
            ];
        } else {
            $c[$mimeType]['example'] = $content;
        }
        
        $responseExample = [
            'description' => $description,
            'content' => $c,
        ];
        
        return $responseExample;
    }
    
    public function getProperties($data)
    {
        $properties = [];
        
        foreach ($data as $key => $value) {
            $type = gettype($value);
            
            if ($type == 'object') {
                $type = get_class($value);
                $newModelName = $this->saveModel($type);

                if ($newModelName) {
                    $properties[$key] = [
                        '$ref' => '#/components/schemas/'.$newModelName,
                    ];
                }
            }
            else if ($type == 'array') {
                $properties[$key] = [
                    'type' => 'object',
                    'properties' => $this->getProperties($value),
                ];
            }
            else {
                $properties[$key] = [
                    'type' => $type,
                ];
            }
            
        }
        
        return $properties;
    }
    
    protected function saveModel($object) {
        $objectName = Arr::last(explode('\\', $object));
        if ($pos = strPos($objectName, 'Resource')) {
            $params = $this->annotationReader->getMethodAnnotations($object, 'fields');
            $info = $this->annotationReader->getClassAnnotations($object);
            
            $modelName = substr($objectName, 0, $pos);
            
            $properties = [];
            
            foreach ($params->toArray() as $key => $value) {
                $type = substr($value, strPos($value, '{') + 1, $typeEnd = strPos($value, '}') - 1);
                
                if (class_exists($type)) {
                    $newModelName = $this->saveModel($type);
                    
                    if ($newModelName) {
                        $properties[$key] = [
                            '$ref' => '#/components/schemas/'.$newModelName,
                        ];
                        continue;
                    }
                }
                
                $description = substr($value, $typeEnd + 3);
                $properties[$key] = [
                    'type' => $type,
                    'description' => $description,
                ];
            }
            
            $this->data['components']['schemas'][$modelName] = [
                'description' => $info->get('description', ''),
                'type' => 'object',
                'properties' => $properties,
            ];
            
            return $modelName;
        }
        
        return false;
    }
    
//    protected function getResponseDescription($code) {
//        $request = $this->getConcreteRequest();
//
//        return elseChain(
//            function() use ($request, $code) {
//                return empty($request) ? Response::$statusTexts[$code] : null;
//            },
//            function() use ($request, $code) {
//                return $this->annotationReader->getClassAnnotations($request)->get("_{$code}");
//            },
//            function() use ($code) {
//                return config("auto-doc.defaults.code-descriptions.{$code}");
//            },
//            function() use ($code) {
//                return Response::$statusTexts[$code];
//            }
//        );
//    }

}
