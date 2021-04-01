<?php

namespace DLW\Models;

use DLW\Models\Deposit;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DLW\Models\Notification;
use DLW\Models\ClientSetting;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'view_id', 'client_id', 'client_secret', 'account_name', 'password',
    ];

    protected $hedden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'admin_notification', 'admin_id', 'notification_id');
    }

    public function permissions()
    {
        return $this->hasMany(ClientSetting::class, 'user_id');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'user_id', 'id');
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class, 'admin_id', 'id');
    }

    public function client_details()
    {
        return $this->hasOne(ClientDetail::class, 'email', 'email');
    }

    public function password_resets()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function access_histories()
    {
        return $this->hasOne(AcessHistory::class, 'user_id', 'id');
    }


}