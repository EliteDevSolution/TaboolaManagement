<?php

namespace DLW\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DLW\Models\Notification;

class Admin extends Authenticatable
{
    use Notifiable;
    protected $guard='admin';

    protected $fillable = [
        'name', 'email','view_id', 'client_id', 'client_secret', 'account_name', 'password',
    ];

    protected $hedden = [
        'password', 'remember_token',
    ];

    public function roles(){
        return $this->belongsTo('App\Models\Role');
    }
    
    public function notifications(){
        return $this->belongsToMany(Notification::class, 'admin_notification', 'admin_id', 'notification_id');
    }
}
