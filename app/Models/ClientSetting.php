<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;
use DLW\Models\Admin;

class ClientSetting extends Model
{
    protected $table = 'client_settings';
    protected $fillable = ['user_id', 'page_key', 'show_rule'];

    public function permissions()
    {
        return $this->belongsTo(Admin::class);
    }

}
