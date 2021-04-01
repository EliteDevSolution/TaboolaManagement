<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDetail extends Model
{
    protected $table = 'client_details';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'email', 'email');
    }

}
