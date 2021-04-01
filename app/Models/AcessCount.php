<?php

namespace DLW\Models;

use Illuminate\Database\Eloquent\Model;

class AcessCount extends Model
{
    protected $table = 'accesscounts';
    protected $fillable = ['id', 'count'];
}
