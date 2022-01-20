<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pool extends Model
{
    public function categories()
    {
        return $this->hasMany(Categories::class, 'pool_id');
    }
}
