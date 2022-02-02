<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Laboratory extends Model
{

    public static function getLaboratoriesById($ids)
    {
        $list = self::whereIn('id', $ids)->pluck('name', 'id');
        return $list;
    }
}
