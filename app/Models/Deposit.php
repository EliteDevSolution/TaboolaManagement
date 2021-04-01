<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = ['user_id', 'made_date', 'amount'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }

    public function client_details()
    {
        return $this->hasManyThrough(Admin::class, ClientDetail::class);
    }
}
