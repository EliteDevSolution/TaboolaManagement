<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class AcessHistory extends Model
{
    protected $table = 'access_histories';
    protected $fillable = ['user_id', 'balance', 'ip_address', 'updated_at'];


    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
