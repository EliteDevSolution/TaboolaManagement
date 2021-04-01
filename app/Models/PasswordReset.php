<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'email', 'email');
    }
}
