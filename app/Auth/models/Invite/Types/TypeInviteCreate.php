<?php

namespace App\Auth\Types;

use App\Auth\Models\Invite;
use App\System\Classes\Type;
use App\System\Exceptions\ServerException;
use App\System\Models\Model;

class TypeInviteCreate extends Type
{
    const required = [ 'invited', 'contact', ];
    
    /** @var Model */
    public $invited;
    
    /** @var string */
    public $contact;
    
    /** @var string */
    public $contactType = 'email';
    
    /** @var Model */
    public $inviter = null;
    
    
    /**
     * @param  string  $value
     * @return string
     */
    public static function setContactType($value)
    {
        if (!isset(Invite::contactTypes[$value])) {
            throw new ServerException("Field format with name contactType is wrong");
        }
        
        return $value;
    }
}
