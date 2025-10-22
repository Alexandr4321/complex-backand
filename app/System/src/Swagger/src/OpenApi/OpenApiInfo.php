<?php


namespace App\System\Swagger\OpenApi;


class OpenApiInfo
{
    protected $title = '';
    
    protected $description = '';
    
    protected $termsOfService = '';
    
    protected $version = '';
    
    protected $contact = [];
    
    protected $licence = [];
    
    
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * @param $termOfService
     */
    public function setTermsOfService($termsOfService)
    {
        $this->termsOfService = $termsOfService;
    }
    
    /**
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
    
    /**
     * @param string $name
     * @param string $url
     * @param string $email
     */
    public function setContact($name = null, $url = null, $email = null)
    {
        $contact = [];
    
        if ($name) {
            $contact['name'] = $name;
        }
        if ($name) {
            $contact['url'] = $url;
        }
        if ($name) {
            $contact['email'] = $email;
        }
        
        $this->contact = $contact;
    }
    
    /**
     * @param string $name
     * @param string $url
     */
    public function setLicence($name, $url = null)
    {
        $licence = [
            'name' => $name,
        ];
        
        if ($url) {
            $licence['url'] = $url;
        }
        
        $this->licence = $licence;
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
    
        if ($this->title) {
            $result['title'] = $this->title;
        }
        if ($this->description) {
            $result['description'] = $this->description;
        }
        if ($this->termsOfService) {
            $result['termsOfService'] = $this->termsOfService;
        }
        if ($this->version) {
            $result['version'] = $this->version;
        }
        if ($this->contact) {
            $result['contact'] = $this->contact;
        }
        if ($this->licence) {
            $result['licence'] = $this->licence;
        }
        
        return $result;
    }
}
