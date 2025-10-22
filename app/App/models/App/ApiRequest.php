<?php


namespace App\App;


use Illuminate\Support\Arr;

class ApiRequest
{
    public $host = '';

    public $headers = [];

    public $type = 'json';

    public $auth = ''; // login:password

    public $otherCert = null; // login:password


    public function __construct($options = [])
    {
        $this->options($options);

        return $this;
    }

    public function options($options)
    {
        $this->host = Arr::get($options, 'host', $this->host);
        $this->type = Arr::get($options, 'type', $this->type);
        $this->headers = Arr::get($options, 'headers', $this->headers);
        $this->auth = Arr::get($options, 'auth', $this->auth);
        $this->otherCert = Arr::get($options, 'otherCert', $this->otherCert);
    }

    public function get($path)
    {
        return $this->request($path, 'get');
    }

    public function post($path, $body = [])
    {
        return $this->request($path, 'post', $body);
    }

    public function put($path, $body = [])
    {
        return $this->request($path, 'put', $body);
    }

    public function patch($path, $body = [])
    {
        return $this->request($path, 'patch', $body);
    }

    public function delete($path)
    {
        return $this->request($path, 'delete');
    }

    protected function request($path, $method, $body = null)
    {
        $headers = $this->headers;

        if ($this->type === 'json') {
            $headers = array_merge($headers, [
                'Accept: application/json',
                'Content-Type: application/json',
            ]);
            $body = $body ? json_encode($body) : null;
        } elseif ($this->type === 'xml') {
            $headers = array_merge($headers, [
                'Accept: application/xml',
                'Content-Type: application/xml',
            ]);
        } else {
            $body = $body ? http_build_query($body) : null;
        }

        //open connection
        $ch = curl_init();


        if ($this->otherCert) {
            curl_setopt ($ch, CURLOPT_CAINFO, database_path('/files/certs/root.cer'));
        }

        curl_setopt($ch,CURLOPT_URL, $this->host.$path);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);

        if ($method === 'post') {
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
        }
        if ($method === 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
        }
        if ($method === 'patch') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
        }
        if ($method === 'delete') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch,CURLOPT_POSTFIELDS, $body);
        }

        if ($this->auth) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth);
        }

        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_VERBOSE, true);


        $result = curl_exec($ch);

        if ($this->type === 'json') {
            $result = json_decode($result, true);
        }

        return $result;
    }
}
