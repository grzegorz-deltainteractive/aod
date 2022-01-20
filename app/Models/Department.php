<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Department extends Model
{

    /**
     * get specific lists
     * @param $ids
     * @return mixed
     */
    public static function getDepartmentsById($ids)
    {
        $list = self::whereIn('id', $ids)->pluck('name', 'id');
        return $list;
    }
}
