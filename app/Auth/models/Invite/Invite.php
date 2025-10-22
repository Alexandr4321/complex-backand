<?php

namespace App\Auth\Models;

use App\Auth\Notifications\CompanyInviteNotify;
use App\Auth\Types\TypeInviteCreate;
use App\Company\Models\Company;
use App\System\Models\Model;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Класс используется в связке с основным, например с User.
 * Позволяет использовать основной класс до фактической регистрации пользователя.
 */
class Invite extends Model
{
    use Notifiable;
    
    protected $table = 'auth__invites';
    
    protected $attributes = [
        'contactType' => 'email',
        'inviterId' => null,
        'inviterType' => null,
        'resolvedAt' => null,
        'sendAt' => null,
    ];
    
    protected $casts = [
        'id' => 'integer',
        'invitedId' => 'integer', // кого приглашают
        'invitedType' => 'string',
        'token' => 'string',
        'contact' => 'string',
        'contactType' => 'string', // key from self::contactTypes
        'inviterId' => 'integer', // кто приглашает, если null то приглашение от админов системы
        'inviterType' => 'string',
        'resolvedAt' => 'datetime',
        'sendAt' => 'datetime', // последняя отправка письма
        'createdAt' => 'datetime',
    ];
    
    public const max = [
        'invitedType' => 63,
        'inviterType' => 63,
        'contact' => 255,
    ];
    
    public const invitedTypes = [
        'user' => User::class,
        'company' => Company::class,
    ];
    
    public const inviterTypes = [
        'user' => User::class,
        'company' => Company::class,
    ];
    
    public const contactTypes = [
        'email' => CompanyInviteNotify::class,
        'phone' => CompanyInviteNotify::class,
    ];
    
    protected static function attributes()
    {
        return [
            'token' => Str::uuid(),
        ];
    }
    
    
    /*************
     * Relations *
     *************/
    
    public function invited()
    {
        return $this->morphTo(null, 'invitedType', 'invitedId');
    }
    
    public function inviter()
    {
        return $this->morphTo(null, 'inviterType', 'inviterId');
    }
    
    
    /**************
     * Operations *
     **************/
    
    /**
     * @param  TypeInviteCreate  $data
     * @return Invite
     */
    public static function create($data)
    {
        $model = new self();
        
        $model->invited()->associate($data->invited);
        $model->contact = $data->contact;
        $model->contactType = $data->contactType;
        if ($data->inviter) {
            $model->inviter()->associate($data->inviter);
        }
        $model->save();
        
        $model->send();
        
        return $model;
    }
    
    /**
     * Отправить приглашение
     */
    public function send()
    {
        $notifyClass = self::contactTypes[$this->contactType];
        $this->notify(new $notifyClass($this->inviter, $this->token));
        
        $this->sendAt = Carbon::now();
        $this->save();
    }
    
    /**
     * Пометить приглашение как принятое
     */
    public function resolve()
    {
        $this->acceptedAt = Carbon::now();
        $this->save();
    }
}
